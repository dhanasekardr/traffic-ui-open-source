<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends BaseController {

    function __construct(){
        helper('form');  // Load the form helper
    }
    
    public function index(): string {

        // Pass the data to the view
        return view('home');
    }

    // Check account status
    private function check_email_exists($email){

        // Connect to the database
        $db = \Config\Database::connect();
        $builder = $db->table('inbounds');

        // Use query binding to prevent SQL injection
        $query = $builder->select('settings')->get();
        $results = $query->getResult();

        if ($results) {
            foreach ($results as $result) {
                // Decode the JSON string in the 'settings' field
                $settings = json_decode($result->settings);

                // Ensure the JSON decoding was successful
                if ($settings && isset($settings->clients)) {
                    foreach ($settings->clients as $client) {
                        if ($client->email == $email && $client->enable == true) {
                            return 1;
                        }
                    }
                }else{
                    return 0;
                }
            }
        }else{
            return 0;
        }
    }

    // Get CPU usage percentage
    private function getCpuUsage() {
        // Execute shell command to get CPU usage
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows command
            $cpuUsage = shell_exec("wmic cpu get loadpercentage");
            $cpuUsage = (float) trim($cpuUsage); // Convert to float and remove any extra spaces or newlines
        } else {
            // Unix/Linux command
            $cpuUsage = sys_getloadavg()[0]; // Get the 1-minute load average
        }
        return number_format($cpuUsage, 2); // Round to two decimals
    }

    // Get RAM usage percentage
    private function getRamUsage(){

        $ramUsagePercent = 0;

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows command
            $ramUsage = shell_exec("wmic os get freephysicalmemory /value");
        } else {
            // Linux/Unix command
            $free = shell_exec("free -m");
            $freeArr = explode("\n", $free);
            $memory = explode(" ", preg_replace('/\s+/', ' ', $freeArr[1]));
            $ramUsage = $memory[2] . 'MB used out of ' . $memory[1] . 'MB';
            $ramUsagePercent = ($memory[2]/$memory[1])*100;
        }
        return [$ramUsage,$ramUsagePercent];
    }

    // Get disk space usage
    private function getDiskSpace(){
        // Execute shell command to get disk usage
        $diskSpace = disk_free_space("/");
        $totalSpace = disk_total_space("/");
        $usedSpace = $totalSpace - $diskSpace;
        return round(($usedSpace / $totalSpace) * 100, 2) . '% used';
    }

    // Get the server's overall status (could be more detailed in the future)
    private function getServerStatus(){
        return 'Online'; // Changed from 'Healthy' to match frontend display
    }

    // Check for uuid
    private function isUuid($input) {
        // Regular expression for validating UUID (v4)
        return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $input);
    }

    // Get data usage for user
    public function getusage(){

        $data = new \stdClass(); // Use the global namespace explicitly
        $data->success = FALSE;
        $data->error = FALSE;
        $data->error_msg = "";
        $data->token = csrf_hash();

        $post_data = $this->request->getPost();  // Using the correct CodeIgniter method to get POST data
        // return $this->response->setJSON($data);

        // Get the email from the GET request
        $email = $post_data['email'];
        $email = preg_replace('/^\s+|\s+$/u', '', $email);

        // Validate if email is provided
        if (!$email) {
            $data->error = TRUE;
            $data->error_msg = "User parameter is missing.";
            return $this->response->setJSON($data);
        }

        $uuid = '';
        $username = '';

        // check input type (uuid or username)
        if ($this->isUuid($email)) {
            $uuid = $email;
        }else{
            $username = $email;
        }

        // Connect to the database
        $db = \Config\Database::connect();

        // Path to the panel.txt file
        $get_panel_type = FCPATH . 'panel.txt';  // FCPATH points to the root directory of your CodeIgniter project

        // Check if the panel type file exists
        if (file_exists($get_panel_type)) {
            // Read the file contents
            // 1 = 3x-ui, 2 = marzban, 3 = hidify
            $panel_number = file_get_contents($get_panel_type);

            // 3x-ui
            if($panel_number == 1){

                // uuid
                if($uuid != ''){

                    $builder = $db->table('inbounds');

                    // Use query binding to prevent SQL injection
                    $query = $builder->select('settings')->get();
                    $results = $query->getResult();

                    if ($results) {
                        foreach ($results as $result) {
                            // Decode the JSON string in the 'settings' field
                            $settings = json_decode($result->settings);

                            // Ensure the JSON decoding was successful
                            if ($settings && isset($settings->clients)) {
                                foreach ($settings->clients as $client) {
                                    if ($client->id == $uuid) {
                                        $username = $client->email;
                                        $uuid = $client->id;
                                        break 2; // Exit both loops when the match is found
                                    }
                                }
                            }else{
                                // No data found for the given email
                                $data->error = TRUE;
                                $data->error_msg = "No data found for the given user.";
                                return $this->response->setJSON($data);
                            }
                        }
                    }else{
                        // No data found for the given email
                        $data->error = TRUE;
                        $data->error_msg = "No data found for the given user.";
                        return $this->response->setJSON($data);
                    }
                }

                $builder = $db->table('client_traffics');
        
                // Use query binding to prevent SQL injection
                $query = $builder->select('enable, email, up, down, expiry_time, total')
                                 ->where('email', $username)
                                 ->get();

            }elseif ($panel_number == 2) {

                $builder = $db->table('users');
        
                // Use query binding to prevent SQL injection
                $query = $builder->select('username, used_traffic, expire, data_limit')
                                 ->where('username', $username)
                                 ->get();
            }
        } else {
            $data->error = TRUE;
            $data->error_msg = "System error.";
            return $this->response->setJSON($data);
        }
        
        // Get the result
        $result = $query->getRow();

        if ($result) {
            // Data found
            $enable = $result->enable == 0 ? 0 : $this->check_email_exists($username);
            $email = $result->email;
            $up = $result->up;
            $down = $result->down;
            $expiry_time = $result->expiry_time;
            $total = $result->total;

            // Convert bytes to GB
            $up_gb = $up / 1073741824;
            $down_gb = $down / 1073741824;
            $total_gb = $total / 1073741824;

            // Format the storage sizes
            function formatStorage($size) {
                if ($size >= 1000) {
                    $size_in_tb = $size / 1000;
                    return number_format($size_in_tb, 2) . ' TB';
                } else {
                    return number_format($size, 2) . ' GB';
                }
            }

            $formatted_up = formatStorage($up_gb);
            $formatted_down = formatStorage($down_gb);
            $formatted_total = formatStorage($total_gb);

            $total_up_down = $up + $down;
            $total_up_down_gb = $total_up_down / 1073741824;
            $formatted_total_up_down = formatStorage($total_up_down_gb);

            $percentage = 0; // Default to 0

            // Check if total is not zero to avoid division by zero
            if ($total > 0) {
                $percentage = ($total_up_down / $total) * 100;
            }

            // Calculate the remaining time
            if($expiry_time != 0){
                $expiry_time_seconds = $expiry_time / 1000;
                $expiry_date = new \DateTime();
                $expiry_date->setTimestamp($expiry_time_seconds);
                $current_date = new \DateTime();
                $interval = $current_date->diff($expiry_date);
                $remaining_days = $interval->format('%r%a');
                $remaining_days = $remaining_days <= 0 ? 0 : $remaining_days;
            }else{
                $remaining_days = "∞";
            }
            
            $ram_usage = $this->getRamUsage();

            // success data
            $data->success = TRUE;
            $data->enable = $enable;
            $data->email = $email;
            $data->uuid = $uuid;
            $data->total = $total_gb == 0 ? "∞ GB" : $formatted_total;
            $data->total_up_down = $formatted_total_up_down;
            $data->percentage = number_format($percentage, 2);
            $data->remaining_days = $remaining_days;
            $data->uptime = shell_exec('uptime -p'); // Example output: "up 5 days, 3 hours"
            $data->cpu_usage = $this->getCpuUsage();
            $data->ram_usage = $ram_usage[0];
            $data->ram_usage_percent = number_format((float)$ram_usage[1], 2, '.', '');
            $data->disk_space = $this->getDiskSpace();
            $data->server_status = $this->getServerStatus();

            return $this->response->setJSON($data);

        } else {
            // No data found for the given email
            $data->error = TRUE;
            $data->error_msg = "No data found for the given user.";
            return $this->response->setJSON($data);
        }
    }
}
