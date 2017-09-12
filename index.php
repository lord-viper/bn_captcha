<?php
	include_once('bn_captcha.php');

    $captcha = new bn_captcha();
    if(isset($_POST['captcha']) and $captcha->check_captcha($_POST['captcha']))
    echo 'captcha enterd is true';
    else
    echo 'captcha enterd is false';

?>
<form method="post">
<table>
<tr>
	<td>captcha test</td>
</tr>
<tr>
	<td><?php echo $captcha->show(5,'num');?></td>
	<td><input type="text" name="captcha" /></td>
</tr>
<tr>
	<td><input type="submit" name="submit" value="submit" /></td>
</tr>
</table>
</form>
<?php

    /**
    //show captcha length 3
    $captcha = new bn_captcha();
    echo $captcha->show(3);
    //show captcha length 5 & only number & background color is #c3c3c3
    $captcha = new bn_captcha();
    echo $captcha->show(5,'num','#c3c3c3');
    //show captcha length 5 & only character & background color is #a0a0a0 and text color is #323232
    $captcha = new bn_captcha();
    echo $captcha->show(5,'chr','#a0a0a0','#323232');
    //show captcha text = hello & length 5 & only character & background color is #ffaa00 and text color is #888888
    $captcha = new bn_captcha();
    echo $captcha->text('hello')->show(5,'chr','#ffaa00','#888888');
    //show captcha drow 3 random vertical line for noise &length 4 & character only
    $captcha = new bn_captcha();
    echo $captcha->line(3)->show(5,'chr');
    //show captcha drow 3 ellipse noise &length 5 & character only & background color is #ffffff and text color is #888888
    $captcha = new bn_captcha();
    echo $captcha->ellipse(3,true)->show(5,'chr','#ffffff','#888888');
    */
?>