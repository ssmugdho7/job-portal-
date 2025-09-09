<?php
session_start();
session_destroy();
header("Location: index.php");

exit;
?>


<script>
    localStorage.removeItem('chatHistory'); // Clear chatbot history
    window.location.href = "index.php";
</script>