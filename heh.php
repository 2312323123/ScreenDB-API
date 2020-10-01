<?php
if(isset($_GET['pass']))
echo password_hash($_GET['pass'] . "41220cb4326079f231ac3ca5a0389da3", PASSWORD_BCRYPT);
else
echo 'no text detected';