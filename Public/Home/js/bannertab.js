//<![CDATA[
$(function(){
	(function(){
		var m=$("#js .aa").size();
		/*$("#jsNav .trigger").eq(0).css("background","url(images/yuan2.png) no-repeat")*/
		var curr = 0;
		$("#jsNav .trigger").each(function(i){
			$(this).click(function(){
								   if($(this).hasClass("xz")){
					}
					else{
					curr = i;
					$("#js .aa").eq(0).show();
					$("#js .aa").children("img").fadeOut("slow");
					$("#js .aa").eq(i).children("img").fadeIn("slow");
					/*$(this).css("background","url(images/yuan2.png) no-repeat")
					$(this).siblings("a").css("background","url(images/yuan1.png) no-repeat")*/
					$("#jsNav .trigger").removeClass("xz");
					$("#jsNav .trigger").eq(i).addClass("xz");
					$(".jsnrk .trigger").removeClass("xz");
					$(".jsnrk .trigger").eq(i).addClass("xz")
					}
				return false;
			});
		});
		
		$(".jsnrk .trigger").each(function(i){
			$(this).click(function(){
					curr = i;
					$("#js .aa").eq(0).show();
					$("#js .aa").children("img").fadeOut("slow");
					$("#js .aa").eq(i).children("img").fadeIn("slow");
					/*$(this).css("background","url(images/yuan2.png) no-repeat")
					$(this).siblings("a").css("background","url(images/yuan1.png) no-repeat")*/
					$("#jsNav .trigger").removeClass("xz");
					$("#jsNav .trigger").eq(i).addClass("xz");
					$(".jsnrk .trigger").removeClass("xz");
					$(".jsnrk .trigger").eq(i).addClass("xz")
				return false;
			});
		});
				
		var pg = function(flag){
			//flag:true��ʾǰ���� false��ʾ��
			if (flag) {
				if (curr == 0) {
					todo = m-1;
				} else {
					todo = (curr - 1) % m;
				}
			} else {
				todo = (curr + 1) % m;
			}
			$("#jsNav .trigger").eq(todo).click();
			$(".jsnrk .trigger").eq(todo).click();
		};
		
		//ǰ��
		$("#prev").click(function(){
			pg(true);
			return false;
		});
		
		//��
		$("#next").click(function(){
			pg(false);
			return false;
		});
		
		//�Զ���
		var timer = setInterval(function(){
			todo = (curr + 1) % m;
			$("#jsNav .trigger").eq(todo).click();
			$(".jsnrk .trigger").eq(todo).click();
		},8000);
		
		//�����ͣ�ڴ�������ʱֹͣ�Զ���
		/*$("#jsNav a").hover(function(){
				clearInterval(timer);
			},
			function(){
				timer = setInterval(function(){
					todo = (curr + 1) % m;
					$("#jsNav .trigger").eq(todo).click();
					$(".jsnrk .trigger").eq(todo).click();
				},8000);			
			}
		);
		$("#js img").hover(function(){
				clearInterval(timer);
			},
			function(){
				timer = setInterval(function(){
					todo = (curr + 1) % m;
					$("#jsNav .trigger").eq(todo).click();
					$(".jsnrk .trigger").eq(todo).click();
				},7500);			
			}
		);*/
	})()
	
	function resize(){
			  $('#js>a.aa img').css('margin-left',($(window).width()-1660)/2);
			  $('.banner .zy').css('margin-left',($(window).width()-1200)/2);
			  $('.banner .jsNav').css('margin-right',($(window).width()-1200)/2);
			  }
		  
		 resize();
		  $(window).resize(function(){
			    resize();
			  })
});

//]]>

//����ͼ�� www.lanrentuku.com

