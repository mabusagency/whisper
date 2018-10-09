<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class CampaignHelper
{

    public function install_lp_files($directory, $campaign=null, $ftp_server=null, $ftp_username=null, $ftp_password=null, $ftp_path=null) {
        
        if($ftp_server) {
            config([
                'filesystems.disks.ftp.host' => $ftp_server,
                'filesystems.disks.ftp.username' => $ftp_username,
                'filesystems.disks.ftp.password' => $ftp_password
            ]);
            Storage::disk('ftp')->makeDirectory($ftp_path . $directory);
        } else {
            Storage::makeDirectory('public/campaigns/' . $directory);
        }

        //Upload Landing page
        $lp_files = Storage::allFiles('public/lp_templates/basic');

        foreach($lp_files as $lp_file) {
            $contents = Storage::get($lp_file);

            //Update path for local testing
            $contents = str_replace(env('APP_URL'),'',$contents);

            $pathInfo = pathinfo($lp_file);
            $fileName = $pathInfo['basename'];
            if(strstr($lp_file,'/assets')) {
                $fileName = 'assets/'.$fileName;
            }

            if($ftp_server) {
                Storage::disk('ftp')->put($ftp_path . $directory . '/'. $fileName, $contents);
            }
            else {
                Storage::put('public/campaigns/' . $directory . '/' . $fileName, $contents);
            }
        }

        //Update htaccess file
        if($campaign && $ftp_server) {
            $htaccess_file = $ftp_path . '.htaccess';
            $htaccess_script = $this->htaccess_script($campaign, $directory);

            if(Storage::disk('ftp')->exists($htaccess_file)) {
                $htaccess_content = Storage::disk('ftp')->get($htaccess_file);

                $search_pattern = "/(#PURL CODE)(?:[\w\W\r\n]*?)(#END PURL CODE)/i";
                $new_content = preg_replace($search_pattern, $htaccess_script, $htaccess_content);

                //add purl code if does not exists
                if(!strstr($new_content,'#PURL CODE')) {
                    $htaccess_script = str_replace('\$1','$1',$htaccess_script);
                    $htaccess_script = str_replace('\$2','$2',$htaccess_script);
                    $new_content = $htaccess_script."\n\n".$htaccess_content;
                }

                Storage::disk('ftp')->put($htaccess_file, $new_content);

            } else {
                Storage::disk('ftp')->put($htaccess_file, $htaccess_script);
            }

        }


    }


    public function htaccess_script($campaign, $directory) {

        $code = '#PURL CODE
RewriteEngine on
RewriteCond %{SCRIPT_FILENAME} !([A-Za-z0-9_-]+)\.(html?|php|asp|css|jpg|gif|shtml|htm|xhtml|txt|ico|xml)/?$ [NC]
RewriteCond %{SCRIPT_FILENAME} ([A-Za-z0-9_-]+)\.([A-Za-z0-9_-]+)/?$ [NC]
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([A-Za-z0-9_-]+)\.([A-Za-z0-9_-]+)/?$ http://'.$campaign->domain.'/'.$directory.'?purl=\$1\$2&campaign_id='.$campaign->id.' [R,L]
#END PURL CODE';

        return $code;
    }


}