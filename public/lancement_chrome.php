<html>
<head>
<HTA:APPLICATION
APPLICATIONNAME="Ouvrir des liens avec le navigateur Chrome"
BORDER="THIN"
BORDERSTYLE="NORMAL"
ICON="Explorer.exe"
INNERBORDER="NO"
MAXIMIZEBUTTON="NO"
MINIMIZEBUTTON="NO"
SCROLL="NO"
SELECTION="NO"
SINGLEINSTANCE="YES"/>
<META HTTP-EQUIV="MSThemeCompatible" CONTENT="YES">
<title>Ouvrir des liens avec le navigateur Chrome</title>

<?php
require_once('CRFC/ntlm_pic.php');
   $browser = getBrowser();
	$host = getHost();


 if ( $browser['name'] != 'Google Chrome' ) {
 ?>

<SCRIPT LANGUAGE="VBScript">
'************************************************************************************
Option Explicit
 Function Executer(StrCmd,Console)
	Dim ws,MyCmd,Resultat
	Set ws = CreateObject("wscript.Shell")
'La valeur 0 pour cacher la console MS-DOS
	If Console = 0 Then
		MyCmd = "CMD /C " & StrCmd & " "
		Resultat = ws.run(MyCmd,Console,True)
	End If
'La valeur 1 pour montrer la console MS-DOS
	If Console = 1 Then
		MyCmd = "CMD /K " & StrCmd & " "
		Resultat = ws.run(MyCmd,Console,True)
	End If
	Executer = Resultat
End Function
'************************************************************************************
Sub window_onload
	Call Executer("CD %programfiles%\Google\Chrome\Application\ & start chrome.exe <?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>",0)
End Sub
'************************************************************************************
Sub CenterWindow(x,y)
	Dim iLeft,itop
	window.resizeTo x,y
	iLeft = window.screen.availWidth/2 - x/2
	itop = window.screen.availHeight/2 - y/2
	window.moveTo ileft,itop
End Sub
'**************************************************************************************
Sub Sleep(MSecs)' Fonction pour faire une pause car wscript.sleep ne marche pas dans un HTA
	Dim fso,objOutputFile
    Set fso = CreateObject("Scripting.FileSystemObject")
    Dim tempFolder : Set tempFolder = fso.GetSpecialFolder(2)
    Dim tempName : tempName = "Sleeper.vbs"
    If Fso.FileExists(tempFolder&"\"&tempName)=False Then
        Set objOutputFile = fso.CreateTextFile(tempFolder&"\"&tempName, True)
        objOutputFile.Write "wscript.sleep WScript.Arguments(0)"
        objOutputFile.Close
    End If
    CreateObject("WScript.Shell").Run tempFolder&"\"&tempName &" "& MSecs,1,True
End Sub
'**************************************************************************************
</script>

</head>
<BODY text="black" bgcolor="White" TOPMARGIN="1" LEFTMARGIN="1">
<META HTTP-EQUIV="Refresh" CONTENT="1; URL=http://fcp10-spsc.ca-technologies.fr/sites/CNET/Pages/Accueil.aspx">

</body>
<?php 
} else {
?>
</head>
<BODY text="black" bgcolor="White" TOPMARGIN="1" LEFTMARGIN="1">
<META HTTP-EQUIV="Refresh" CONTENT="1; URL=<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>">

</body>
<?php
}
?>
</html>