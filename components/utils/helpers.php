<?php
function unique_id() {
    $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charLength = strlen($chars);
    $randomString = "";

    for ($i=0; $i < 20; $i++) { 
        $randomString .= $chars[mt_rand(0, $charLength - 1)];
    }
    return $randomString;
}

function to_slug($str) {
  // Convert to lowercase
  $str = strtolower($str);

  // Replace spaces with hyphens
  $str = str_replace(' ', '-', $str);

  // Remove invalid characters
  $str = preg_replace('/[^a-z0-9-]/', '', $str);

  // Remove consecutive hyphens
  $str = preg_replace('/-+/', '-', $str);

  // Trim leading/trailing hyphens
  $str = trim($str, '-');

  return $str;
}

?>