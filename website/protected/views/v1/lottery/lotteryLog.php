<!DOCTYPE html>
<html>
<head>
   <title>中奖记录列表</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
   <link href="http://cdn.bootcss.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<style type="text/css">
	body{
		background-color: gray
	}
	.item{padding: 1.8rem 4rem;background-color: #fff;}
	.time{color: #B9B2B2}
	.icon{margin-top: 1.8rem;float: right}
</style>
<body>
<div class="row" id="container">
   
</div>


<script type="text/javascript" src="http://libs.baidu.com/jquery/2.1.1/jquery.min.js"></script>
<script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

<script type="text/javascript">
	function date(Time){
	          var d = new Date(Time * 1000);    //根据时间戳生成的时间对象
	          var now =new Date();
	          if(d.getFullYear()==now.getFullYear()&&d.getMonth()==now.getMonth()&&d.getDate()==now.getDate()){
	            var date="今天"+" "+(d.getHours()) + ":" +(d.getMinutes())+":"+(d.getSeconds());
	          }else{
	            var date=d.getFullYear()+"-"+(d.getMonth() + 1) + "-" +(d.getDate()) + " " +(d.getHours()) + ":" +(d.getMinutes())+":"+(d.getSeconds());
	          }
	          return date;
	    }

	$(document).ready(function(){
		//初始化 页面
		var initUrl="<?php echo Yii::app()->request->baseUrl; ?>/index.php/v2/lottery/InitLotteryLog?benben_id="+<?php echo $benben_id;?>;
		$.post(initUrl,{},function(data){
			// console.log(data);
			obj = document.getElementById("container"); 
			for(var i in data.items){
				console.log(data.items[i]);
				var div1=document.createElement("div");
				var div2=document.createElement("div");
				var h1=document.createElement("h1");
				var h3=document.createElement("h3");
				var div3=document.createElement("div");
				var h2=document.createElement("h2");
				var span=document.createElement("span");
				// 加内容
				if(data.items[i].lottery_num==9){
					h1.appendChild(document.createTextNode("实物大奖"));
					h1.setAttribute("class", "text-danger"); 
					h2.setAttribute("class", "text-danger icon");
					h2.appendChild(document.createTextNode("实物大奖")); 
				}else{
					h1.appendChild(document.createTextNode("犇币"));
					h1.setAttribute("class", "text-success");
					h2.setAttribute("class", "text-primary icon"); 
					h2.appendChild(document.createTextNode("+"+data.items[i].lottery_num/10)); 
				}
				// h1.appendChild(document.createTextNode(data.items[i].lottery_num)); 
				h3.appendChild(document.createTextNode(date(data.items[i].lottery_time))); 
				
				// 加属性 
				div1.setAttribute("class", "row item"); 
				div2.setAttribute("class", "col-sm-8 col-md-8 col-lg-8"); 
				div3.setAttribute("class", "col-sm-4 col-md-4 col-lg-4"); 


				h3.setAttribute("class", "time");

				div2.appendChild(h1);
				div2.appendChild(h3);
				div3.appendChild(h2);
				div1.appendChild(div2);
				div1.appendChild(div3);
                obj.appendChild(div1); 
			}
		},'json');

	});
</script>

</body>
</html>