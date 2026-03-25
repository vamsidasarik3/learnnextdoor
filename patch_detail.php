
$file = 'app/Views/frontend/detail.php';
$content = file_get_contents($file);

$old = "      if(!res.success){\r\n        var errs = res.errors ? Object.values(res.errors).join(' ') : (res.message||'Error');\r\n        showAlert(errs); return;\r\n      }";

$new = "      if(!res.success){\r\n        if(res.auth_required){\r\n          if(bModal) bModal.hide();\r\n          window.location.href = (res.login_url || (BASE + 'login'));\r\n          return;\r\n        }\r\n        var errs = res.errors ? Object.values(res.errors).join(' ') : (res.message||'Error');\r\n        showAlert(errs); return;\r\n      }";

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "Patched successfully.\n";
} else {
    // Try LF line endings
    $old2 = str_replace("\r\n", "\n", $old);
    if (strpos($content, $old2) !== false) {
        $new2 = str_replace("\r\n", "\n", $new);
        $content = str_replace($old2, $new2, $content);
        file_put_contents($file, $content);
        echo "Patched (LF) successfully.\n";
    } else {
        echo "PATTERN NOT FOUND. Snippet around line 816:\n";
        $lines = explode("\n", $content);
        for ($i = 812; $i < 825; $i++) {
            echo ($i+1).": ".addcslashes($lines[$i] ?? '', "\r\n")."\n";
        }
    }
}
