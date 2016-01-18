<h1>会员登录</h1>
<form method="post" action="/index.php/v1/user/login">
<input type="hidden" value ="iphone" name="key" />
手机号<input name="phone" /></br>
密码<input name="password" /></br>
<input type="submit" value="确定" />
</form>

<h1>忘记密码</h1>
<form method="post" action="/index.php/v1/user/fogpwd">
<input type="hidden" value ="iphone" name="key" />
手机号<input name="phone" /></br>
密码<input name="password" /></br>
重复密码<input name="repassword" /></br>
验证码<input name="code" /></br>
<input type="submit" value="确定" />
</form>

<h1>注册信息</h1>
<form method="post" action="/index.php/v1/user/register">
<input type="hidden" value ="iphone" name="key" />
手机号<input name="phone" /></br>
昵称<input name="nick_name" /></br>
年龄<input name="age" /></br>
性别<input name="sex" /></br>
密码<input name="password" /></br>
重复密码<input name="repassword" /></br>
验证码<input name="code" /></br>
<input type="submit" value="确定" />
</form>

<h1>修改图像</h1>
<form method="post"  enctype="multipart/form-data" action="/index.php/v1/user/updateavatar">
<input type="hidden" value ="iphone" name="key" />
头像<input type="file" name="poster" /></br>
<input type="submit" value="确定" />
</form>

<h1>修改密码</h1>
<form method="post" action="/index.php/v1/user/changepwd">
<input type="hidden" value ="iphone" name="key" />
密码<input name="oldpassword" /></br>
新密码<input name="password" /></br>
<input type="submit" value="确定" />
</form>

<h1>修改信息</h1>
<form method="post" action="/index.php/v1/user/update">
<input type="hidden" value ="iphone" name="key" />
姓名<input name="name" /></br>
昵称<input name="nick_name" /></br>
年龄<input name="age" /></br>
性别<input name="sex" /></br>
<input type="submit" value="确定" />
</form>

<h1>通讯录匹配</h1>
<form method="post" action="/index.php/v1/contact/match">
<input type="hidden" value ="iphone" name="key" />
分组<input name="group" /></br>
手机<textarea type="text" name="phone"  style="width:200px" ></textarea></br>
<input type="submit" value="确定" />
</form>

<h1>通讯录匹配1</h1>
<form method="post" action="/index.php/v1/contact/newmatch">
<input type="hidden" value ="iphone" name="key" />
分组<input name="group" /></br>
手机<textarea type="text" name="phone"  style="width:200px" ></textarea></br>
<input type="submit" value="确定" />
</form>

<h1>新建群组</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/group/add">
<input type="hidden" value ="iphone" name="key" />
姓名<input name="name" /></br>
头像<input type="file"  name="poster" /></br>
省<input name="province" /></br>
市<input name="city" /></br>
区<input name="region" /></br>
街<input name="street" /></br>
简介<input name="description" /></br>
广告<input name="notice" /></br>
<input type="submit" value="确定" />
</form>

<h1>修改群组信息</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/group/edit">
<input type="hidden" value ="iphone" name="key" />
群组ID<input name="groupid" /></br>
姓名<input name="name" /></br>
头像<input type="file"  name="poster" /></br>
简介<input name="description" /></br>
广告<input name="notice" /></br>
<input type="submit" value="确定" />
</form>

<h1>新建号码直通车</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/store/add">
<input type="hidden" value ="iphone" name="key" />
号码直通车名称<input name="name" /></br>
号码直通车简称<input name="short_name" /></br>
封面<input type="file"  name="poster" /></br>
手机号码<input name="phone" /></br>
固话<input name="telephone" /></br>
行业<input name="industry" /></br>
省<input name="province" /></br>
市<input name="city" /></br>
区<input name="area" /></br>
街<input name="street" /></br>
纬度<input name="lat" /></br>
经度<input name="lng" /></br>
服务项目<input name="tag" /></br>
详细地址<input name="address" /></br>
简介<input name="description" /></br>

<input type="submit" value="确定" />
</form>

<h1>号码直通车信息完善</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/store/info">
<input type="hidden" value ="iphone" name="key" />
姓名<input name="name" /></br>
证件号<input name="id_card" /></br>
封面1<input type="file"  name="poster1" /></br>
封面2<input type="file"  name="poster2" /></br>
手机号码<input name="phone" /></br>

<input type="submit" value="确定" />
</form>

<h1>微创作发布</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/creation/create">
<input type="hidden" value ="iphone" name="key" />
封面1<input type="file"  name="img1" /></br>
封面2<input type="file"  name="img2" /></br>
封面3<input type="file"  name="img3" /></br>
封面4<input type="file"  name="img4" /></br>
封面5<input type="file"  name="img5" /></br>
封面6<input type="file"  name="img6" /></br>
简介<input name="description" /></br>
好友联盟ID<input name="league_id" /></br>
地区<input name="region" /></br>

<input type="submit" value="确定" />
</form>

<h1>朋友圈发布</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/friend/create">
<input type="hidden" value ="iphone" name="key" />
封面1<input type="file"  name="img1" /></br>
封面2<input type="file"  name="img2" /></br>
封面3<input type="file"  name="img3" /></br>
封面4<input type="file"  name="img4" /></br>
封面5<input type="file"  name="img5" /></br>
封面6<input type="file"  name="img6" /></br>
简介<input name="description" /></br>

<input type="submit" value="确定" />
</form>

<h1>微创作评论</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/creation/comment">
<input type="hidden" value ="iphone" name="key" />
微创作ID<input name="creationid" /></br>
评论内容<input name="content" /></br>

<input type="submit" value="确定" />
</form>

<h1>朋友圈点赞</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/friend/laud">
<input type="hidden" value ="iphone" name="key" />
朋友圈ID<input name="friendid" /></br>

<input type="submit" value="确定" />
</form>

<h1>新建政企通讯录</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/enterprise/add">
<input type="hidden" value ="iphone" name="key" />
名称<input name="name" /></br>
类型<input name="type" /></br>
省<input name="province" /></br>
市<input name="city" /></br>
区<input name="area" /></br>
街<input name="street" /></br>
简介<input name="description" /></br>
短号<input name="short_phone" /></br>

<input type="submit" value="确定" />
</form>

<h1>百姓网申请</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/bxapply/join">
<input type="hidden" value ="iphone" name="key" />
姓名<input name="name" /></br>
证件号<input name="id_card" /></br>
封面1<input type="file"  name="poster1" /></br>
封面2<input type="file"  name="poster2" /></br>
手机号码<input name="phone" /></br>
省<input name="province" /></br>
市<input name="city" /></br>
区<input name="area" /></br>
街<input name="street" /></br>

<input type="submit" value="确定" />
</form>

<h1>创建好友联盟</h1>
<form method="post" enctype="multipart/form-data"  action="/index.php/v1/league/add">
<input type="hidden" value ="iphone" name="key" />
名字<input name="name" /></br>
描述<input name="description" /></br>
省<input name="province" /></br>
市<input name="city" /></br>
区<input name="area" /></br>
封面<input type="file"  name="poster" /></br>


<input type="submit" value="确定" />
</form>