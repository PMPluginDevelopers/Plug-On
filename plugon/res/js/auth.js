// Handle login
var lastPost = 0;
var waiting = false;

const REG_ERR_NAME_TAKEN    = 0x10;
const REG_ERR_EMAIL_TAKEN   = 0x11;
const REG_ERR_UNKNOWN       = 0x00;
const REG_ERR_INVALID_PASS  = 0x02;
const REG_ERR_INVALID_EMAIL = 0x03;
const REG_ERR_INVALID_NAME  = 0x04;
const REG_SUCCESS           = 0x01;

function login(form) {
    if((Date.now() / 1000 | 0) - lastPost < 2 || waiting === true) {
        console.log("Cool down!");
        return false;
    }
    $(".error-log").html("<img id='loading-ico' src='/images/loading.gif' width='48' height='48'></img>")
    $(".loading-ico").show();
    var formData = new FormData(form);
    waiting = true;
    ajax("loginProcess", {
        data: {
            username: formData.get("username"),
            password: formData.get("password"),
            remember: false // not implemented
        },
        success: function(response) {
            if(response.status == 'OK') {
                window.location.href = "/";
            } else {
                $(".error-log").html("<li class='error-badge'>Wrong password and/or username</li>");
            }
            $(".loading-ico").hide();
            waiting = false;
        }
    });
    lastPost = Date.now() / 1000 | 0
    return false;
}

function register(form) {
    if((Date.now() / 1000 | 0) - lastPost < 2 || waiting === true) {
        console.log("Cool down!");
        return false;
    }
    $(".error-log").html("<img id='loading-ico' src='/images/loading.gif' width='48' height='48'></img>")
    $(".loading-ico").show();
    var formData = new FormData(form);
    waiting = true;
    ajax("registerProcess", {
        data: {
            username: formData.get("username"),
            password: formData.get("password"),
            email: formData.get("email")
        },
        success: function(response) {
            if(response.status === REG_SUCCESS) {
                alert("You've successfully registered! Now login with your username and password.");
                window.location.href = "/login";
            } else {
                $('.error-log').html("");
                response.errors.foreach(function(error){
                    switch(error) {
                        case REG_ERR_EMAIL_TAKEN:
                            $(".error-log").append("<li class='error-badge'>Wrong password and/or username</li>");
                            break;
                        case REG_ERR_NAME_TAKEN:
                            $(".error-log").append("<li class='error-badge'>Username already taken</li>");
                            break;
                        case REG_ERR_INVALID_EMAIL:
                            $(".error-log").append("<li class='error-badge'>Invalid E-Mail</li>");
                            break;
                        case REG_ERR_INVALID_NAME:
                            $(".error-log").append("<li class='error-badge'>Invalid username</li>");
                            break;
                        case REG_ERR_INVALID_PASS:
                            $(".error-log").append("<li class='error-badge'>Invalid password</li>");
                            break;
                        default:
                        case REG_ERR_UNKNOWN:
                            $(".error-log").append("<li class='error-badge'>Unknown error occurred, please try again later.</li>");
                    }
                });
                
            }
            $(".loading-ico").hide();
            waiting = false;
        }
    });
    lastPost = Date.now() / 1000 | 0
    return false;
}