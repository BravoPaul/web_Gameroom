/**
 * Created by paul on 04/03/16.
 */
var xmlHttp;
var myUserName;

function loginUser()
{

    document.getElementById("txtHint").innerHTML="";

    var user = $("#user").val();
    var password = $("#password").val();

    myUserName = user;

    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    }
    var url="getuser.php";
    url=url+"?user="+user;
    url=url+"&password="+password;
    xmlHttp.onreadystatechange=stateChanged;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function signUp()
{

    var user = $("#user2").val();
    var password = $("#password2").val();
    var email = $("#email").val();

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    }
    var url="signUp.php";
    url=url+"?user="+user;
    url=url+"&password="+password;
    url=url+"&email="+email;
    xmlHttp.onreadystatechange=stateChanged2;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);

}

function stateChanged()
{


    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
    {
        if(xmlHttp.responseText=="true"){
            var url = "gameroom/thumbnail.php"
            window.location.href=url;
            setTimeout("javascript:location.href=url", 50);

        }
        if(xmlHttp.responseText=="false"){
            document.getElementById("txtHint").innerHTML="wrong username or password, please input again";
        }
    }
}

function stateChanged2()
{
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
    {
        if(xmlHttp.responseText!=1){
           alert("The username is already used")
        }
    }

    $("#signUp").show();

}

function GetXmlHttpObject()
{
    var xmlHttp=null;
    try
    {
        // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    }
    catch (e)
    {
        //Internet Explorer
        try
        {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}
