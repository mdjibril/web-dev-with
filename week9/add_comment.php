<?php
// Simulating an unescaped database fetch
$malicious_input = "<script>alert('XSS Exploit: Session Cookie Stolen!');</script>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>XSS Demonstration</title>
</head>
<body>
    <h3>User Reviews</h3>
    
    <!-- VULNERABLE: Direct rendering of raw values -->
    <div style="border: 1px solid red; padding: 10px; margin-bottom: 20px;">
        <h5>Unsafe Display (Vulnerable to XSS):</h5>
        <?php echo $malicious_input; ?>
    </div>

    <!-- SECURE: Encoded with htmlspecialchars -->
    <div style="border: 1px solid green; padding: 10px;">
        <h5>Safe Display (Secured against XSS):</h5>
        <?php echo htmlspecialchars($malicious_input, ENT_QUOTES, 'UTF-8'); ?>
    </div>
</body>
</html>
