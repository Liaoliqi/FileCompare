<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script type="text/javascript" src="./jquery-3.1.1.min.js"></script>
	<title>Document</title>
	<style>
		.bag{
			background-color:#00ffff;
		}
		.yellow{
			background-color:#f2b661;
		}
	</style>
</head>
<body>
<form id='formAjax'>
	<div class="yincan">
		<label id='url_one'>
			URL_ONE： <input type="text" name='url_one' style="width:58%;" value="<?php echo @$_COOKIE['fileUrl1']?$_COOKIE['fileUrl1']:'' ?>">
			<input type="button" id="cookie1" value="保存">
			<label><input type="radio" name="status" value="1" checked>根据文件大小</label>
			<label><input type="radio" name="status" value="0">根据文件内容</label>
		</div>
		<div id='url_two'>
			URL_TWO： <input type="text" name='url_two' style="width:58%;" value="<?php echo @$_COOKIE['fileUrl2']?$_COOKIE['fileUrl2']:'' ?>">
			<input type="button" id="cookie2" value="保存">
		</div>
		<div id='filter'>
			过滤目录：<input type="text" name='filter' style="width:58%;" value=".idea,.settings,.buildpath,.svn,data,public,服务化文档,功能设计文档">
			<input type="button" value="刷新" onclick="location.reload()">
		</div>
	</div>
	<input type="button" id="determine" value="确定">
</form>
<div id='center'>
	<div class="yincan" style="float: left;width:50%"><span id="count1"></span><div id="html1"></div></div>
	<div class="yincan" style="float: left;width:50%"><span id="count2"></span><div id="html2"></div></div>
</div>
<div id="juedui" align="center" style="display:none;">
	<img src="./material.gif" alt="请稍后。。。">
</div>
<div id="content" style="width:80%;height:80%;position:fixed; top:50px; left:120px;z-index:99;background-color:#5b6b85;display:none;border:1px solid red">
	<div id="left" style="width:49%;height:100%;float:left;background-color:#fff;overflow-y:scroll; overflow-x:scroll;"></div>
	<div id="right" style="width:49%;height:100%;float:right;background-color:#fff;overflow-y:scroll; overflow-x:scroll;"></div>
</div>
</body>
<script>
	$(function(){
		$(document).keyup(function(event){
			if(event.keyCode ==13){
				$('#determine').trigger("click");
			}
		});
		$('#cookie1').click(function(){
			var fileUrl=$("input[name='url_one']").val();
			setCookie('fileUrl1',fileUrl,365);
			alert('保存成功');
		});
		$('#cookie2').click(function(){
			var fileUrl=$("input[name='url_two']").val();
			setCookie('fileUrl2',fileUrl,365);
			alert('保存成功');
		});
	});
	$('#determine').click(function(){
		$('#juedui').css('display','block');
		$('#count1').html('');
		$('#count2').html('');
		$('#html1').html('');
		$('#html2').html('');
	 	var postData='';
    	postData=$('#formAjax').serialize();
	    $.ajax({
        url: './doFile.php',
        dataType: "json",
        type: "post",
        data: postData,
        success: function (result) {
         	if(result.success){
				$('#juedui').css('display','none');
				$('#count1').html('URL_ONE不同文件路径('+result.count1+')<input id="add1" type="button" value="查看新增文件">');
				$('#count2').html('URL_TWO不同文件路径('+result.count2+')<input id="add2" type="button" value="查看新增文件">');
				$('#html1').html(result.html1);
				$('#html2').html(result.html2);
				//锁定输入框
				$(":input").attr('readonly',true);
				bindEvent();
			}else{
				$('#juedui').css('display','none');
				alert(result.message);
			}
        },
        error:function(result){
			$('#juedui').css('display','none');
        	alert('AjaxRequestError');
        }
        });
	});
	function bindEvent(){
		//文件点击事件
		$('.html1').on('click',function(){
			$('.html1').removeClass('bag');
			var str=$(this).text();
			str=str.replace(/\\/g, "\\\\");
			var has=".html1:contains("+str+")";
			$(has).addClass('bag');
		});
		//查看新增按钮点击事件
		$('#add1').on('click',function(){
			$('.html1').removeClass('bag');
			$('.add1').addClass('bag');
			$('.add1').each(function(){
				var _this=$(this);
				var str=$(this).text();
				var _html=str.replace(/\\/g, "\\\\");
				$('.add2').each(function(){
					var str2=$(this).text();
					var _html2=str2.replace(/\\/g, "\\\\");
					if(_html==_html2){
						_this.removeClass('bag');
						return false;
					}
				});
			});
		});
		$('#add2').on('click',function(){
			$('.html1').removeClass('bag');
			$('.add2').addClass('bag');
			$('.add2').each(function(){
				var _this=$(this);
				var str=$(this).text();
				var _html=str.replace(/\\/g, "\\\\");
				console.log(_html);
				$('.add1').each(function(){
					var str2=$(this).text();
					var _html2=str2.replace(/\\/g, "\\\\")
					if(_html==_html2){
						_this.removeClass('bag');
						return false;
					}
				});
			});
		});
		//比较差异事件
		$(document).keyup(function(event){
			if(event.keyCode ==68){
				//判断该文件是否可以比较
				var arr1=$('.add1.bag');
				var arr2=$('.add2.bag');
				if((arr1.length==0 && arr2.length==0) || arr1.length>1 || arr2.length>1){
					alert('不能对比该文件');
					return false;
				}
				var url1='';
				var url2='';
				if(arr1.length>0){
					url1=($("input[name='url_one']").val())+(arr1.text());
//					url1=JSON.stringify(url1);
//					url1 = url1.substring(1,url1.length-1);
				}
				if(arr2.length>0){
					url2=($("input[name='url_two']").val())+(arr2.text());
//					url2=JSON.stringify(url2);
//					url2 = url2.substring(1,url2.length-1);
				}
				var postData={
					url1:url1,
					url2:url2
				};
				$.ajax({
					url: './fileContent.php',
					dataType: "json",
					type: "post",
					data: postData,
					success: function (result) {
						if(result.success){
							$('#left').html(result.content1);
							$('#right').html(result.content2);
							$('#content').css('display','block');
						}else{
							alert('文件目录出错');
						}
					},
					error:function(result){
						alert('AjaxRequestError');
					}
				});
			}
		});
		$('.yincan').on('click',function(){
			$('#content').css('display','none');
		});
	}
	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		var expires = "expires=" + d.toUTCString();
		document.cookie = cname + "=" + cvalue + "; " + expires+";path=/";
	}
</script>
</html>