console.info("Help us to improve Plugon on Github: https://github.com/PMPluginDevelopers/Plug-On");

String.prototype.hashCode = function() {
    var hash = 0, i, chr, len;
    if(this.length === 0) return hash;
    for(i = 0, len = this.length; i < len; i++) {
        chr = this.charCodeAt(i);
        hash = ((hash << 5) - hash) + chr;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
};
String.prototype.ucfirst = function() {
    return this.charAt(0).toUpperCase() + this.substr(1)
};

function isLoggedIn() {
    return "${session.isLoggedIn}" == "true";
}

var toggleFunc = function() {
    var $this = $(this);
    var name = $this.attr("data-name");
    console.assert(name.length > 0);
    var children = $this.children();
    if(children.length == 0) {
        $this.append("<h2 class='wrapper-header'>" + name + "</h2>");
        return;
    }
    var wrapper = $("<div class='wrapper'></div>");
    wrapper.attr("id", "wrapper-of-" + name.hashCode());
    wrapper.css("display", "none");
    $this.wrapInner(wrapper);
    var header = $("<h2 class='wrapper-header'></h2>");
    header.html(name);
    header.append("&nbsp;&nbsp;");
    var img = $("<img title='Expand Arrow' width='24'>");
    img.attr("src", "https://maxcdn.icons8.com/Android_L/PNG/24/Arrows/expand_arrow-24.png");
    var clickListener = function() {
        var wrapper = $("#wrapper-of-" + name.hashCode());
        if(wrapper.css("display") == "none") {
            wrapper.css("display", "block");
            img.attr("src", "https://maxcdn.icons8.com/Android_L/PNG/24/Arrows/collapse_arrow-24.png");
        } else {
            wrapper.css("display", "none");
            img.attr("src", "https://maxcdn.icons8.com/Android_L/PNG/24/Arrows/expand_arrow-24.png");
        }
    };
    header.click(clickListener);
    header.append(img);
    $this.prepend(header);

    if($this.attr("data-opened") == "true") {
        clickListener();
    }
};
var navButtonFunc = function() {
    var $this = $(this);
    var target = $this.attr("data-target");
    var ext;
    if(!(ext = $this.hasClass("extlink"))) {
        target = "${path.relativeRoot}" + target;
    }
    var wrapper = $("<a></a>");
    wrapper.addClass("navlink");
    wrapper.attr("href", target);
    if(ext) {
        wrapper.attr("target", "_blank");
    }
    $this.wrapInner(wrapper);
};
var hoverTitleFunc = function() {
    var $this = $(this);
    $this.click(function() {
        alert($this.attr("title"));
    });
};
var timeTextFunc = function() {
    var $this = $(this);
    var timestamp = Number($this.attr("data-timestamp")) * 1000;
    var date = new Date(timestamp);
    var now = new Date();
    var text;
    if(date.toDateString() == now.toDateString()) {
        text = date.toLocaleTimeString();
    } else {
        text = $this.attr("data-multiline-time") == "on" ?
            (date.toLocaleDateString() + date.toLocaleTimeString()) : date.toLocaleString();
    }
    $this.text(text);
};
var timeElapseFunc = function() {
    var $this = $(this);
    var time = Math.round(new Date().getTime() / 1000 - Number($this.attr("data-timestamp")));
    var out = "";
    var hasDay = false;
    var hasHr = false;
    if(time >= 86400) {
        out += Math.floor(time / 86400) + " d ";
        time %= 86400;
        hasDay = true;
    }
    if(time >= 3600) {
        out += Math.floor(time / 3600) + " hr ";
        time %= 3600;
        hasHr = true;
    }
    if(time >= 60) {
        out += Math.floor(time / 60) + " min ";
        time %= 60;
    }
    if(out.length == 0 || time != 0) {
        if(!hasDay && !hasHr) out += time + " s";
    }
    $this.text(out.trim());
};
var domainFunc = function() {
    $(this).text(window.location.origin);
};
var dynamicAnchor = function() {
    var $this = $(this);
    var parent = $this.parent();
    parent.hover(function() {
        $this.css("display", "inline");
    }, function() {
        $this.css("display", "none");
    });
};

$(document).ready(function() {
    fixSize();
    $(window).resize(fixSize);
    $(".toggle").each(toggleFunc);
    $(".navbutton").each(navButtonFunc);
    $(".hover-title").each(hoverTitleFunc);
    $(".time").each(timeTextFunc);
    var timeElapseLoop = function() {
        $(".time-elapse").each(timeElapseFunc);
        setTimeout(timeElapseLoop, 1000);
    };
    $(".domain").each(domainFunc);
    timeElapseLoop();
    $(".dynamic-anchor").each(dynamicAnchor);
});

function fixSize() {
    $("#body").css("top", $("#header").outerHeight());
}

function ajax(path, options) {
    $.post("/csrf", {}, function(token) {
        if(options === undefined) {
            options = {};
        }
        if(options.dataType === undefined) {
            options.dataType = "json";
        }
        if(options.data === undefined) {
            options.data = {};
        }
        if(options.method === undefined) {
            options.method = "POST";
        }
        options.data.csrf = token;
        $.ajax("/" + path, options).fail(function(response, code){
            console.log("ajax failed: " + code);
        });
    });
}

function logout() {
    ajax("logout", {
        success: function() {
            window.location.reload(true);
        }
    });
}

function promptDownloadResource(id, defaultName) {
    var name = prompt("Filename to download with:", defaultName);
    if(name === null) {
        return;
    }
    window.location = "${path.relativeRoot}r/" + id + "/" + name + "?cookie";
}