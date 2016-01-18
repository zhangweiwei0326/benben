<?php
$content = 'Haha前三季度经济数据一发布，立即引来外媒关注和热议。美媒报道称，这是自全球金融危机以来，中国经济增速首次低于7%。外界对中国经济的担忧开始加剧';
$code_content = urlencode(iconv("utf8","gbk",$content));
$nickname = '习近平访英nick';
$code_nick = iconv("utf8","gbk",$nickname);
$a = file_get_contents('http://180.168.88.73:8080/dragontv/txly_interface.jsp?sql=liuyan&bu=smgtm&phone=13910101010&area=1&content='.$code_content.'&nickname='.$code_nick);
var_dump($a);
?>