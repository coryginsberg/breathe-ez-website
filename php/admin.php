<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "cory.ginsberg1@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "c50d83" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'9440' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYWhkaHVqRxUSmMExlaHWY6oAkFtDKEMow1SEgAEWM0ZUh0NFBBMl906YuXboyMzNrGpL7WF1FWlkb4eogsFU01DU0EEVMoBXsFhQ7gG4BiaG4BZubByr8qAixuA8Aaz/MXJt9M7AAAAAASUVORK5CYII=',
			'593A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGVqRxQIaWFtZGx2mOqCIiTQ6NAQEBCCJBQYAxRodHUSQ3Bc2benSrKkrs6Yhu6+VMRBJHVSMAWheYGgIsh2tLCAxFHUiU0BuQdXLGgByMyOqeQMUflSEWNwHAHlXzOZri7IaAAAAAElFTkSuQmCC',
			'6D79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZWgICAhAEgtoEWl0aAh0EEEWawCKNTrCxMBOioyatjJr6aqoMCT3hUwBqpvCMBVFbytQLABoF5qYowMDih0gt7A2MKC4BezmBgYUNw9U+FERYnEfAOSqzWL45xG+AAAAAElFTkSuQmCC',
			'8FA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEx1QBITmSLSwBDKEBCAJBbQKtLA6OjoIICmjrUh0AHZfUujpoYtXRWZmoXkPqg6DPNYQwMdRNDFGlDFIHoDUPSyBoDFUNw8UOFHRYjFfQCYRczEKlR6TwAAAABJRU5ErkJggg==',
			'0608' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLI6OjoIIIkFtAq0sDaEABTB3ZS1NJpYUtXRU3NQnJfQKtoK5I6mN5G14ZAFPNAdjii2YHNLdjcPFDhR0WIxX0APBPLgro+R7gAAAAASUVORK5CYII=',
			'0C40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxkaHVqRxVgDWIEiDlMdkMREpog0AEUCApDEAlpFGhgCHR1EkNwXtXTaqpWZmVnTkNwHUsfaCFeHEAsNRBED29GIagfYLY2obsHm5oEKPypCLO4DAGUmzS62mK/VAAAAAElFTkSuQmCC',
			'E8F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA6Y6IIkFNLC2sjYwBASgiIk0ujYwOghgqGN0QHZfaNTKsKWhK1OzkNwHVYfVPBGCYphuAbu5gQHFzQMVflSEWNwHAFx9zFcUidCuAAAAAElFTkSuQmCC',
			'6648' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoEWlkmOroIIIs1gDkBcLVgZ0UGTUtbGVm1tQsJPeFTBFtZW1EM69VpNE1NBDVPKCYQyOqHWC3oOnF5uaBCj8qQizuAwDBZc2I7mO3QAAAAABJRU5ErkJggg==',
			'7F3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1DGUMdkEVbRRpYGx0dAtDEGBoCHUSQxaYAeQh1EDdFTQ1bNXVlaBaS+xgdUNSBIWsDpnkiWMQCGjDdAhJjRHfzAIUfFSEW9wEA4LDMBv9zdGMAAAAASUVORK5CYII=',
			'DE68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGaY6IIkFTBFpYHR0CAhAFmsVaWBtcHQQwRBjgKkDOylq6dSwpVNXTc1Cch9YHVbzArGYhyaGxS3Y3DxQ4UdFiMV9ANRPzYRSf2JEAAAAAElFTkSuQmCC',
			'80EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUNDkMREpjCGsDYwOiCrC2hlbUUXE5ki0uiKEAM7aWnUtJWpoStDs5Dch6YOah42MWx2YLoF6mYUsYEKPypCLO4DACa1yNrkw4hGAAAAAElFTkSuQmCC',
			'7749' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHaY6IIu2MjQ6tDoEBKCLTXV0EEEWmwIUDYSLQdwUtWraysysqDAk9zE6MASwAnUj62UFirKGBjQgi4kARYG2oNgBVtGI6haoGKqbByj8qAixuA8AyGPMsMy0NRsAAAAASUVORK5CYII=',
			'6F2A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGVqRxUSmiDQwOjpMdUASC2gRaWBtCAgIQBZrEAGSgQ4iSO6LjJoatmplZtY0JPeFAM1jaGWEqYPobQXypjCGhqCLBaCqA7vFAVWMNQDoltBAFLGBCj8qQizuAwBPX8s8mSx+dQAAAABJRU5ErkJggg==',
			'A725' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUMDkMRYAxgaHR0dHZDViUxhaHRtCEQRC2hlaGVoCHR1QHJf1NJV01atzIyKQnIfUF0ASKUIkt7QUEYHhimoYgGtrA0MAYwOqGIiQDcyBASgibGGBkx1GAThR0WIxX0A6mTLaqtAlgwAAAAASUVORK5CYII=',
			'1F68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxOog0MDo6BAQgiYkCxVgbHIEksl6QGANMHdhJK7Omhi2dumpqFpL7wOrQzIPoDcRiHqYYhltCgCrQ3DxQ4UdFiMV9AIg5yVCAiNbRAAAAAElFTkSuQmCC',
			'E0C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHUNDkMQCGhhDGB0CGkRQxFhbWRsE0MREGl3BNMJ9oVHTVqauWrUyC8l9UHWtDJh6pzBg2hHAgOGWQAcsbkYRG6jwoyLE4j4A1R7MdguDxGIAAAAASUVORK5CYII=',
			'1248' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHaY6IImxOrC2MrQ6BAQgiYk6iABVOTqIoOgF6gyEqwM7aWXWqqUrM7OmZiG5D6huCmsjqnlAsQDW0EA080AmotvB2sCAplc0RDTUAc3NAxV+VIRY3AcAxgTKQgAeBbIAAAAASUVORK5CYII=',
			'EAD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUMDkMQCGhhDWBsdHRhQxFhbWRsC0cREGl0bAl0dkNwXGjVtZeqqyKgoJPdB1AFJFL2ioZhiYPMcMMQaHQKQ3RcaAhQLZZjqMAjCj4oQi/sAVdLOYl+TAxMAAAAASUVORK5CYII=',
			'276D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMdkMREpjA0Ojo6OgQgiQW0MjS6Njg6iCDrbmVoZW1ghIlB3DRt1bSlU1dmTUN2XwBDAKsjql5GB0YH1oZAFDFWMEQVEwFCRjS3hIYCVaC5eaDCj4oQi/sAGTLKZw7qt44AAAAASUVORK5CYII=',
			'9A3D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGUMdkMREpjCGsDY6OgQgiQW0srYyNAQ6iKCIiTQ6ANWJILlv2tRpK7OmrsyahuQ+VlcUdRDYKgq0E9U8AZB5aGIiU0QaXdHcwhog0uiI5uaBCj8qQizuAwBWZsyIW57fiAAAAABJRU5ErkJggg==',
			'A1F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDAxoCkMRYAxgDWBsYGpHFRKawgsRakcUCWhlAYlMCkNwXtRSIQldFRSG5D6KO0QFZb2goWCw0BNO8Bix2oImxhqKLDVT4URFicR8A6NTLbj2z3cwAAAAASUVORK5CYII=',
			'2FCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQx1CHVqRxUSmiDQwOgRMdUASC2gVaWBtEAgIQNYNFmN0EEF237SpYUtXrcyahuy+ABR1YAjiAcVCQ5Dd0gASE0RRJ9IAcksgilhoKJAX6ogiNlDhR0WIxX0Amy7KtS4BJyQAAAAASUVORK5CYII=',
			'8465' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM3QMQ6AMAgFUBi4Ad6HDt1x6NLTsPQG7RE66Ck1caHqqElh+/khL8D+GIOZ9hcfKRRImNRlXKFhCOJ7erbIxowrRjKM4nw9997blrPzceVCQYyHe0uKprcMCtkqPFoKBlHvu8zQZIL/fbgvvgOzs8tDsC+6FgAAAABJRU5ErkJggg==',
			'3A5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUMdkMQCpjCGsDYwOgQgq2xlbQWJiSCLTRFpdJ0KFwM7aWXUtJWpmZlZ05DdB1Tn0BCIqrdVNBRTDGgemlgAUK+joyOKW0QDgOaFMqK4eaDCj4oQi/sACcLLkDGGvScAAAAASUVORK5CYII=',
			'208B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0srayNgQ6iCDrbhVpdESog7hp2rSVWaErQ7OQ3ReAog4MGR1EGl3RzGNtwLRDpAHTLaGhmG4eqPCjIsTiPgCJv8oZZJ22/QAAAABJRU5ErkJggg==',
			'554A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQxkaHVqRxQIaRBoYWh2mOqCLTXUICEASCwwQCWEIdHQQQXJf2LSpS1dmZmZNQ3ZfK0OjayNcHUIsNDA0BNmOVpFGBzR1IlNYgSpRxVgDGEPQxQYq/KgIsbgPAEhezM6ctJz9AAAAAElFTkSuQmCC',
			'9D65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMDkMREpoi0Mjo6OiCrC2gVaXRtwCbG6OqA5L5pU6etTJ26MioKyX2srkB1jg4NIsg2g/UGoIgJgMUCHUQw3OIQgOw+iJsZpjoMgvCjIsTiPgDWJcwJUJDiQAAAAABJRU5ErkJggg==',
			'A379' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QwQ2AIAxFvwc2YCDcoCbCgWnKoRvICFyYUkJiUtSjRtvbT/v6UtRLMf7Ur/hNzqzGU3YqM2QFTEQqsxuS48VZlZFAkOYj60qx1FBLjUH59bkNWe9633jUuCOv0XC6YcUwBheS5swYnL/634N947cDiInMlFoDHFgAAAAASUVORK5CYII=',
			'C301' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENYQximMLQii4m0irQyhDJMRRYLaGRodHR0CEURa2BoZW0IgOkFOylq1aqwpauiliK7D00dTKzRFV0MYgc2t6CIQd0cGjAIwo+KEIv7AGUUzHB1u02HAAAAAElFTkSuQmCC',
			'63B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANYQ1hDGVqRxUSmiLSyNjpMRRYLaGFodG0ICEURa2AAqYPpBTspMmpV2NLQVUuR3RcyBUUdRG8r2DyCYlC3oIhB3RwaMAjCj4oQi/sAbXzNSjZD1WUAAAAASUVORK5CYII=',
			'E776' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA6Y6IIkFNDA0OjQEBARgiAU6CKCKtTI0Ojoguy80atW0VUtXpmYhuQ+oLoBhCiOaeYwODAGMDiIoYqxAUXQxEaAoA4re0BCwGIqbByr8qAixuA8Ak2TM88vmsMkAAAAASUVORK5CYII=',
			'C1F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WEMYAlhDAxoCkMREWhkDWBsYGpHFAhpZQWKtKGINDCCxKQFI7osCoqWhq6KikNwHUcfogKmXMTQExQ6weWhuwRRjDWENRRcbqPCjIsTiPgAoEMtYjXjdCQAAAABJRU5ErkJggg==',
			'0627' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dGkSQxESmiDSyNgSgiAW0gngBQIhwX9TSaWGrVmatzEJyX0CraCsDCKLqbXSYwjCFAc0OhwCGAAZ0tzgwOqC7mTU0EEVsoMKPihCL+wBp88qJ4eRKEAAAAABJRU5ErkJggg==',
			'1C00' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMLQii7E6sDY6hDJMdUASE3UQaXB0dAgIQNEr0sDaEAgkEe5bmTVt1dJVkUAS4T40dXjFMO3A4pYQTDcPVPhREWJxHwACGsmz/iWK1AAAAABJRU5ErkJggg==',
			'7B33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGUIdkEVbRVpZGx0dAlDFGh0aAhpEkMWmiLQygEWR3Bc1NWzV1FVLs5Dcx+iAog4MWRswzRPBIgbkYbgloAGLmwco/KgIsbgPAEAjzfu4k96ZAAAAAElFTkSuQmCC',
			'6F5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUMdkMREpog0sDYwOgQgiQW0QMREkMUagGJT4erAToqMmhq2NDMzNAvJfSFTQLoCUc1rhYiJoImxoomB3MLo6IiilzUAqCKUEcXNAxV+VIRY3AcAbDvLkoblY1UAAAAASUVORK5CYII=',
			'077B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA0MdkMRYAxgaHRoCHQKQxESmQMREkMQCWhlaGRodYerATopaumraqqUrQ7OQ3AdUF8AwhRHFvIBWRgeGAEYU80SmsALdgyrGGiDSABJF1gtSARRDcfNAhR8VIRb3AQDzn8rU5X8dNAAAAABJRU5ErkJggg==',
			'8C3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WAMYQ0HQAUlMZApro2ujo0MAklhAq0iDQ0OggwiKOiAPoQ7spKVR01atmroyNAvJfWjq4OYxoJmH3Q5Mt2Bz80CFHxUhFvcBAGqPzS8bYgH/AAAAAElFTkSuQmCC',
			'7C3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZgZChFUW0lbXRtdFhqgOKmEiDQ0NAQACy2BSRBoZGRwcRZPdFTVu1aurKrGlI7mN0QFEHhqwNIF5gaAiSmEgDyI5AFHUBDSC3OKKJgdzMiCI2UOFHRYjFfQAKGczMSxrVjQAAAABJRU5ErkJggg==',
			'80A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQytrK6OjoIICiTqTRtSHQAdl9S6OmrUxdFZmaheQ+qDo084BioYEOImh2sDagioHcwtoQgKIX5GagGIqbByr8qAixuA8AHoLMa+PqucIAAAAASUVORK5CYII=',
			'9BAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQximMIYGIImJTBFpZQhldEBWF9Aq0ujo6Igu1sraEAgTAztp2tSpYUtXRYZmIbmP1RVFHQQCzXMNRRUTAImhqQO5BV0vyM1AMRQ3D1T4URFicR8ASo7K1rQpCMoAAAAASUVORK5CYII=',
			'DD73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA0IdkMQCpoi0MjQEOgQgi7WKNDo0BDSIoIuBRRHui1o6bWXW0lVLs5DcB1Y3haEBw7wABgzzHB3QxIBuYW1gRHEL2M0NDChuHqjwoyLE4j4AwuXPm8qYifcAAAAASUVORK5CYII=',
			'236F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUNDkMREpoi0Mjo6OiCrC2hlaHRtQBVjaGVoZW1ghIlB3DRtVdjSqStDs5DdFwBUh2YeUBfQvEAUMdYGTDGRBky3hIaC3YzqlgEKPypCLO4DACnHyONdL3ifAAAAAElFTkSuQmCC',
			'A09A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgDGEMYHR2mOiCJiUxhbWVtCAgIQBILaBVpdG0IdBBBcl/U0mkrMzMjs6YhuQ+kziEErg4MQ0OBYg2BoSEo5rG2MjagqgtoBbnFEU0M5GZGFLGBCj8qQizuAwA6H8uMLedGbQAAAABJRU5ErkJggg==',
			'69C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHRoCkMREprC2MjoENCKLBbSINLo2CLSiiDWAxBimBCC5LzJq6dLUVauiopDcFzKFMdC1AWgist5WBqBextAQFDEWkB3Y3IIihs3NAxV+VIRY3AcA+9LOb4wwK1QAAAAASUVORK5CYII=',
			'6D0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQximMLQii4lMEWllCGWY6oAkFtAi0ujo6BAQgCzWINLo2hDoIILkvsioaStTV0VmTUNyX8gUFHUQva1gsdAQNDFHoCUiGG5hRBGDuBlVbKDCj4oQi/sA7rrMpLw/j8EAAAAASUVORK5CYII=',
			'7774' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DAxoCkEVbGRodGgIasYi1oohNAYtOCUB2X9SqaauWroqKQnIfowNDAMMURgdkvaxgUcbQECQxEYgoilsCwKKExQYq/KgIsbgPAF4YzaxSswhbAAAAAElFTkSuQmCC',
			'4593' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGMgChA7JYiEgDo6OjQwCSGCNQjLUhoEEESYx1ikgISCwAyX3Tpk1dujIzamkWkvsCpjA0OoTA1YFhaChQDM08hikijY4YYqyt6G5hmMIYguHmgQo/6kEs7gMAsTTMwZeCdvQAAAAASUVORK5CYII=',
			'6A90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGVqRxUSmMIYwOjpMdUASC2hhbWVtCAgIQBZrEGl0bQh0EEFyX2TUtJWZmZFZ05DcFzJFpNEhBK4OordVNNShAV1MpNERzQ4RoF5HNLewBgDNQ3PzQIUfFSEW9wEA/9zNKpsPT2MAAAAASUVORK5CYII=',
			'37BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANEQ11DGUMDkMQCpjA0ujY6OqCobAWKNQSiik1haGVFqAM7aWXUqmlLQ1eGZiG7bwpDACuGeYwOrOjmtbI2oIsFTBFpQNcrGgAUQ3PzQIUfFSEW9wEARerKhqmt5LYAAAAASUVORK5CYII=',
			'2690' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaWRtCAgIQNbdKtLA2hDoIILsvmnTwlZmRmZNQ3ZfgGgrQwhcHRgyOog0OjSgirE2iDQ6otkBtAHDLaGhmG4eqPCjIsTiPgD1uctIbQNtAgAAAABJRU5ErkJggg==',
			'73F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDAxoCkEVbRVpZGxgaUcUYGl2BJIrYFAaQuikByO6LWhW2NHRVVBSS+xgdQOoYHZD1gsx3bWAMDUESEwGLMaC4JaAB7BY0MaCb0cQGKvyoCLG4DwACMczxZdZ0BwAAAABJRU5ErkJggg==',
			'211D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIY6IImJTGEMYAhhdAhAEgtoZQ1gBIqJIOtuBeuFiUHcNG1V1KppK7OmIbsvAEUdGDI6YIqxNmCKiUDFkN0SGsoayhjqiOLmgQo/KkIs7gMAdsLHsTV/rV0AAAAASUVORK5CYII=',
			'346D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYWhlCGUMdkMQCpjBMZXR0dAhAVglUxdrg6CCCLDaF0ZW1gREmBnbSyqilS5dOXZk1Ddl9U0RaWR3R9LaKhro2BKKJMbSyookB3dKK7hZsbh6o8KMixOI+AOCdympzmxeOAAAAAElFTkSuQmCC',
			'629E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gRaXRtCEQVa2BAFgM7KTJq1dKVmZGhWUjuC5nCMIUhBE1vK0MAA7p5rYwOjGhiQLc0oLuFNUA01AHNzQMVflSEWNwHAKbJyiBNGjL/AAAAAElFTkSuQmCC',
			'43AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37prCGMExhDHVAFgsRaWUIZXQIQBJjDGFodHR0dBBBEmOdwtDK2hAIEwM7adq0VWFLV0VmTUNyXwCqOjAMDWVodA1FFWOYAhRrQBcTAesNQBFjDQGKobp5oMKPehCL+wAPxculNJ/+jwAAAABJRU5ErkJggg==',
			'1DA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQximMIaGIImxOoi0MoQyNIggiYk6iDQ6OjqgiDECxVwbAoAQ4b6VWdNWpq6KWpmF5D6oulYGdL2hAVMwxBoCAtDEWlkbAh2QxURDREPQxQYq/KgIsbgPAChhyprC8ukZAAAAAElFTkSuQmCC',
			'B235' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQ6AIAxFy8AN8D5lcC+JXTgNHbgBHMFBTiluRR01oT/p8PLTvBTaYxLMlF/8mMxm2DApRsVmKx51j7ITTGFkBQTFr6j8OLa91SNG5dd7VzO54R5Q3zdm+v2AAys2WUHSfkwLe4aKE/zvw7z4nRHVzdH90w1RAAAAAElFTkSuQmCC',
			'E2E1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHVqRxQIaWFtZGximooqJNLo2MISiijGAxGB6wU4KjVq1dGnoqqXI7gOqm8KKUAcTC8AUY3TAFGNtQBcLDRENdQ11CA0YBOFHRYjFfQCh+8yL5IluywAAAABJRU5ErkJggg==',
			'DC94' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGRoCkMQCprA2Ojo6NKKItYo0uAJJdDFWoOoAJPdFLZ22amVmVFQUkvtA6hhCAh3Q9TI0BIaGoIk5Al2CxS0oYtjcPFDhR0WIxX0AHbLQI7581w8AAAAASUVORK5CYII=',
			'2CAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIYGIImJTGFtdAhldEBWF9Aq0uDo6IgixgAUY20IhIlB3DRt2qqlqyJDs5DdF4CiDgwZHYBioahirA0iDa5o6oCqGtHFQkMZQ4Hmobh5oMKPihCL+wAu4srKsuh/fAAAAABJRU5ErkJggg==',
			'0476' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nM2QIQ7DQAwE1+B4weU/LijfgCPlJX2FC/yD5AkHklcmKrKVwlapl4209shYD2P4p/zETxReGmcNrBAzjGRgdUKDjXoJjC43vK4a/e6997Uvj2fwo1fHJGkffWhK0ZpvuGhmu4sXQ+q+nQ3J+az/fTEf/Da6K8reL8CvjAAAAABJRU5ErkJggg==',
			'E0C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHaY6IIkFNDCGMDoEBASgiLG2sjYIOoigiIk0ugJpEST3hUZNW5kKpKOQ3AdV1+iAqbeVAcMOgSkMWNyC6WbH0JBBEH5UhFjcBwBWEs0M/NaorQAAAABJRU5ErkJggg==',
			'4F5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37poiGuoY6hjogi4WINLA2MDoEIIkxQsVEkMRYpwDFpsLFwE6aNm1q2NLMzKxpSO4LmAJSEYiiNzQUU4wBZB4WMUZHRxS3gMQYQhlR3TxQ4Uc9iMV9AEyyysSffSRFAAAAAElFTkSuQmCC',
			'2A6C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaYGIImJTGEMYXR0CBBBEgtoZW1lbXB0YEHW3SrS6NrA6IDivmnTVqZOXZmF4r4AoDpHRwdkexkdRENdGwJRxFgbQOYFotghAhRzRHNLaKhIowOamwcq/KgIsbgPAJKQyzXX0A9/AAAAAElFTkSuQmCC',
			'1390' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGVqRxVgdRFoZHR2mOiCJiTowNLo2BAQEoOhlaGVtCHQQQXLfyqxVYSszI7OmIbkPpI4hBK4OJtbo0IAp5ohhBxa3hGC6eaDCj4oQi/sAML/JEy3EpyUAAAAASUVORK5CYII=',
			'C20C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQximMEwNQBITaWVtZQhlCBBBEgtoFGl0dHR0YEEWa2BodG0IdEB2X9SqVUuXrorMQnYfUN0UVoQ6mFgAhlgjowMjmh1AtzSgu4U1RDTUAc3NAxV+VIRY3AcAI4vLTYGsmfAAAAAASUVORK5CYII=',
			'8DE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHVqRxUSmiLSyNjBMRRYLaBVpdG1gCEVTBxKD6QU7aWnUtJWpoauWIrsPTR2yeQTFoG5BEYO6OTRgEIQfFSEW9wEAwR/MtmJ4wMoAAAAASUVORK5CYII=',
			'AEAA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMLQii7EGiDQwhDJMdUASE5ki0sDo6BAQgCQW0CrSwNoQ6CCC5L6opVPDlq6KzJqG5D40dWAYGgoUCw0MDcFtHh4x0VB0sYEKPypCLO4DAPYxzFl4BWOHAAAAAElFTkSuQmCC',
			'0E63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxmA0AFJjDVApIHR0dEhAElMZIpIA2uDQ4MIklhAK0gMSCO5L2rp1LClU1ctzUJyH1ido0NDAIbeABTzIHagimFzCzY3D1T4URFicR8AqcvL01lTRroAAAAASUVORK5CYII=',
			'EDAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQximMIaGIIkFNIi0MoQyOjCgijU6OjpiiLk2BMLEwE4KjZq2MnVVZGgWkvvQ1CHEQrGIYaprZUUTA7kZXWygwo+KEIv7AHBCzH8zxm7WAAAAAElFTkSuQmCC',
			'E41D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMIY6IIkFNDBMZQhhdAhAFQtlBIqJoIgxugL1wsTATgqNWrp01bSVWdOQ3BfQINKKpA4qJhrqgCHGgEUdRAzZLSA3M4Y6orh5oMKPihCL+wAHKctkUDeyywAAAABJRU5ErkJggg==',
			'6DC6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCHaY6IImJTBFpZXQICAhAEgtoEWl0bRB0EEAWawCJMToguy8yatrK1FUrU7OQ3BcyBawO1bxWiF4RDDFBFDFsbsHm5oEKPypCLO4DAA6RzO0gT7YyAAAAAElFTkSuQmCC',
			'B58C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGaYGIIkFTBFpYHR0CBBBFmsVaWBtCHRgQVUXwujo6IDsvtCoqUtXha7MQnZfwBSGRkeEOqh5DI2uQPNQxUTAYqh2sLaiuyU0gDEE3c0DFX5UhFjcBwCMmcyJTxRFkQAAAABJRU5ErkJggg==',
			'5C8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGUMDkMQCGlgbHR0dHRhQxEQaXBsCUcQCA0QaGBHqwE4KmzZt1arQlaFZyO5rRVEHF2NFMy+gFdMOkSmYbmENwHTzQIUfFSEW9wEAP+jKhIGCoCMAAAAASUVORK5CYII=',
			'9FEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUMDkMREpog0sDYwOiCrC2glKAZ20rSpU8OWhq4MzUJyH6srpl4GLOYJYBHD5hbWAKAYmpsHKvyoCLG4DwBXeMj/NOrMfgAAAABJRU5ErkJggg==',
			'0417' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIaGIImxBjBMZQhhaBBBEhOZwhDKiCYW0MroyjAFSCO5L2rp0qWrpq1amYXkvoBWEaAdQHtQ9IqGOkwB6UaxA6QugAHVLSD3OaC7mTHUEUVsoMKPihCL+wAAp8pXs4ccZwAAAABJRU5ErkJggg==',
			'11B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUIdkMRYHRgDWBsdHQKQxEQdWANYGwIaRND1Njo0BCC5b2XWqqiloauWZiG5D00dQgybeVjtQHNLCGsoupsHKvyoCLG4DwBoU8hx2C438gAAAABJRU5ErkJggg==',
			'E5D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGaY6IIkFNIg0sDY6BASgizUEOoigioWwNgTA1IGdFBo1denSVVFTs5DcB5RvdEWoQxLDMA+LGGsrultCQxhD0N08UOFHRYjFfQAszc6gEd/e4gAAAABJRU5ErkJggg==',
			'5CAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMIY6IIkFNLA2OoQyOgSgiIk0ODo6OoggiQUGiDSwNgTC1IGdFDZt2qqlqyJDs5Dd14qiDiEWGohiXgBQzLUBVUxkCmujK5pe1gDGUKB5KG4eqPCjIsTiPgACXcztvTxCGgAAAABJRU5ErkJggg==',
			'1FB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGUIdkMRYHUQaWBsdHQKQxERBYg0BDSIoekHqHBoCkNy3Mmtq2NLQVUuzkNyHpg4hhs08rHaguSUEKIbm5oEKPypCLO4DAFbEysKkgUQ1AAAAAElFTkSuQmCC',
			'EB8A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGVqRxQIaRFoZHR2mOqCKNbo2BAQEYKhzdBBBcl9o1NSwVaErs6YhuQ9NHZJ5gaEhmGLo6jD0QtzMiCI2UOFHRYjFfQAMbczCKBtyYAAAAABJRU5ErkJggg==',
			'A9EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUNDkMRYA1hbWYEyyOpEpog0uqKJBbSiiIGdFLV06dLU0JWhWUjuC2hlDETXGxrKgMU8FiximG4BmgdyM4rYQIUfFSEW9wEADPTJ0KMoB/0AAAAASUVORK5CYII=',
			'E05A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHVqRxQIaGENYGximOqCIsbYCxQICUMREGl2nMjqIILkvNGraytTMzKxpSO4DqXNoCISpQxYLDcGwA10dYwijoyOKGMjNDKGMKGIDFX5UhFjcBwBWY8wmkHsvfAAAAABJRU5ErkJggg==',
			'0F72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxBogAyYCAACQxkSkgsUAHESSxgFYgr9GhQQTJfVFLp4atWgqkkdwHVjcFpBJNbwBDKwOaHYwOQJVobmEFqURxM0iMMTRkEIQfFSEW9wEAM03L47ilBuEAAAAASUVORK5CYII=',
			'3B48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANEQxgaHaY6IIkFTBFpZWh1CAhAVtkqAlTl6CCCLAZSFwhXB3bSyqipYSszs6ZmIbsPqI61EdM819BAVPNAdjSi2gF2C5pebG4eqPCjIsTiPgDECc167LdKkQAAAABJRU5ErkJggg==',
			'0F26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxBog0MDo6BAQgiYlMEWlgbQh0EEASC2gVAZKBDsjui1o6NWzVyszULCT3gdW1MqKYBxabwugggmYHQwCqGNgtDgwoekEqWEMDUNw8UOFHRYjFfQBG0Mqc58ewLwAAAABJRU5ErkJggg==',
			'B875' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA0MDkMQCprC2MjQEOiCrC2gVaXRAFwOpa3R0dUByX2jUyrBVS1dGRSG5D6xuCkODCLp5AZhijg6MDiJodrA2MAQguw/s5gaGqQ6DIPyoCLG4DwB3as0pUbJNzAAAAABJRU5ErkJggg==',
			'AFFB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA0MdkMRYA0QaWIEyAUhiIlMgYiJIYgGtKOrATopaOjVsaejK0Cwk96GpA8PQUNzm4bEDWQzFzQMVflSEWNwHAK/qyzNu3nD7AAAAAElFTkSuQmCC',
			'4365' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37prCGMIQyhgYgi4WItDI6Ojogq2MMYWh0bUAVY53C0MrawOjqgOS+adNWhS2dujIqCsl9ASB1jg4NIkh6Q0NB5gWgiDFMAYkFOqCKgdziEIDiPrCbGaY6DIbwox7E4j4AevbLJ93/vN8AAAAASUVORK5CYII=',
			'2848' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoFQGqcnQQQdbdClQXCFcHcdO0lWErM7OmZiG7L4C1lbUR1TxGB5FG19BAFPNYG4B2NKLaIdIAtANNb2goppsHKvyoCLG4DwDnGMzJP6gvOwAAAABJRU5ErkJggg==',
			'4FF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpI37poiGuoYGtKKIhYg0sDYwTEUWY4SIhSKLsU4Bi8H0gp00bdrUsKWhq5Yiuy8AVR0YhoZiijFgUYdHLDRgMIQf9SAW9wEA5D/LPjRRawAAAAAASUVORK5CYII=',
			'D6C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCHVqRxQKmsLYyOgRMdUAWaxVpZG0QCAhAFWtgbWB0EEFyX9TSaWFLV63MmobkvoBW0VYkdXDzXLGKodmBxS3Y3DxQ4UdFiMV9ANKKzYzNFMhBAAAAAElFTkSuQmCC',
			'8749' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQx0aHaY6IImJTGFodGh1CAhAEgtoBYpNdXQQQVXXyhAIFwM7aWnUqmkrM7OiwpDcB1QXwArULYJiHqMDa2hAA6oYawPQFjQ7REBiKG5hDQCLobh5oMKPihCL+wCGOs0yU6WSGQAAAABJRU5ErkJggg==',
			'4A0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjAEAHErilgIYwhDKMNUByQxxhDWVkZHh4AAJDHWKSKNrg2BDiJI7ps2bdrK1FWRWdOQ3BeAqg4MQ0NFQ4FioSEobhFpdHR0RFEHEnMIZcQUm4ImNlDhRz2IxX0Ag2bL4jrtRyIAAAAASUVORK5CYII=',
			'C438' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYWhlDGaY6IImJtDJMZW10CAhAEgtoZAhlaAh0EEEWa2B0ZUCoAzspatXSpaumrpqaheS+AJCJ6OY1iIY6oJvXyNCKbgdQZyu6W7C5eaDCj4oQi/sAiXLNUZ0kQy0AAAAASUVORK5CYII=',
			'C808' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYQximMEx1QBITaWVtZQhlCAhAEgtoFGl0dHR0EEEWa2BtZW0IgKkDOylq1cqwpauipmYhuQ9NHVRMpNG1IRDVPCx2YHMLNjcPVPhREWJxHwDUA8y7Q+MNDAAAAABJRU5ErkJggg==',
			'1ABA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGVqRxVgdGENYGx2mOiCJiTqwtrI2BAQEoOgVaXRtdHQQQXLfyqxpK1NDgSSS+9DUQcVEQ10bAkND0M1rCERTh6lXNAQoFsqIIjZQ4UdFiMV9AGUyyiNYvitqAAAAAElFTkSuQmCC',
			'A894' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGRoCkMRYA1hbGR0dGpHFRKaINLo2BLQiiwW0srayNgRMCUByX9TSlWErM6OiopDcB1LHEBLogKw3NFSk0aEhMDQExTyRRkegS9DtALoFTQzTzQMVflSEWNwHAI+dzm29jsqqAAAAAElFTkSuQmCC',
			'7BE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHVpRRFtFWlkbGKY6oIo1ujYwBAQgi00BqWN0EEF2X9TUsKWhK7OmIbkPqAJZHRiyNoDMQxUTacC0I6AB0y0BDVjcPEDhR0WIxX0AUCvLnpg3IrgAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>