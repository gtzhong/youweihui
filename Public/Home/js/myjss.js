$(function(){
    l()
    c()
    
    function l() {
        $(".pl-ctlist").eq(0).addClass("pl-cto"),
        $(".ilx-schinput").click(function(e) {
            e.stopPropagation(),
            $(this).parents(".ilx-schtx").addClass("ilx-schtxfd"),
            $(this).parents(".ilx-schtx").siblings().removeClass("ilx-schtxfd"),
            $(this).parents(".ilx-schtx").children(".ilx-schlist").show(),
            $(this).parents(".ilx-schtx").siblings().children(".ilx-schlist").hide()
        }),
        $(".ilx-sch-li").click(function(e) {
            e.stopPropagation();
            var t = $(this).data("type");
            $(this).parents(".ilx-schtx").children(".ilx-schinput").val($(this).html()),
            $(this).parents(".ilx-schlist").hide(),
            $(this).parents(".ilx-schtx").removeClass("ilx-schtxfd"),
            $(this).parents(".ilx-schtx").children(".ilx-schinput").data("utype", t),
            $(this).parents(".ilx-schtx").children(".ilx-schinput").data("can", $(this).data(t))
            typeof $(this).data(t + "catid") != "undefined" && $("#catid").data("can", $(this).data(t + "catid"))
        }),
        $(".ilx-clsprt").click(function() {
            $(this).parents(".ilx-schlist").hide(),
            $(this).parents(".ilx-schtx").removeClass("ilx-schtxfd")
        }),
        $(".pl-ctlist").click(function(e) {
            e.stopPropagation(),
            $(this).addClass("pl-cto").siblings().removeClass("pl-cto");
            var t = $(this).index(".pl-ctlist");
            $(".pl-ctpl").siblings(".pl-ctpl").hide().eq(t).show()
        }),
        $("body").click(function() {
            $(".ilx-schlist").hide(),
            $(".ilx-schtx").removeClass("ilx-schtxfd")
        }),
        $(".ilx-qka").click(function() {
            var e = $(".ilx-schinput").size()
              , t = "./Line/index?"
              , n = [];
            for (i = 0; i < e; i++) {
                typeof $(".ilx-schinput").eq(i).data("can") != "undefined" && n.push($(".ilx-schinput").eq(i).data("utype") + "=" + $(".ilx-schinput").eq(i).data("can"))
            }
            window.open(t + n.join("&"))
        })
    }
    function c() {
        $("#collection").click(function() {
            try {
                window.external.addFavorite(window.location.href, $("title").html())
            } catch (e) {
                try {
                    window.sidebar.addPanel($("title").html(), window.location.href, "")
                } catch (e) {
                    alert("很抱歉，收藏没有成功，您可以通过Ctrl+D进行手动添加")
                }
            }
        })
    }

})
