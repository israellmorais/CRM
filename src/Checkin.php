<?php
/*******************************************************************************
 *
 *  filename    : Checkin.php
 *  last change : 2007-xx-x
 *  description : Quickly add attendees to an event
 *
 *  http://www.churchcrm.io/
 *  Copyright 2001-2003 Phillip Hullquist, Deane Barker, Chris Gebhardt
 *  Copyright 2005 Todd Pillars
 *  Copyright 2012 Michael Wilt
 *
 *  ChurchCRM is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 ******************************************************************************/

$sPageTitle = gettext('Event Checkin');

// Include the function library
require 'Include/Config.php';
require 'Include/Functions.php';
require 'Include/Header.php';


$sAction = '';
$EventID = 0;
$EvtName = '';
$EvtDesc = '';
$EvtDate = '';

if (array_key_exists('Action', $_POST)) {
    $sAction = $_POST['Action'];
}
if (array_key_exists('EventID', $_POST)) {
    $EventID = $_POST['EventID'];
} // from ListEvents button=Attendees
if (array_key_exists('EName', $_POST)) {
    $EvtName = $_POST['EName'];
}
if (array_key_exists('EDesc', $_POST)) {
    $EvtDesc = $_POST['EDesc'];
}
if (array_key_exists('EDate', $_POST)) {
    $EvtDate = $_POST['EDate'];
}

//
// process the action inputs
//

//Start off by first picking the event to check people in for
//include "show_post_info.php";
//include "show_session_info.php";

$sSQL = 'SELECT * FROM events_event';
$rsEvents = RunQuery($sSQL);

//Page loading for the first time
if (!isset($_POST['EventID']) && !isset($_POST['Verify']) && !isset($_POST['Add']) && !isset($_POST['Checkout']) || isset($_POST['Exit'])) {
    ?>
<div class="row">
    <div class="col-md-8 col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= gettext('Select the event to which you would like to check people in for') ?>:</h3>
            </div>
            
            <div class="box-body">      
                <form name="Checkin" action="Checkin.php" method="POST">
                    <?php if ($sGlobalMessage): ?>
                        <p><?= $sGlobalMessage ?></p>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label><?= gettext('Select Event'); ?>:</label>
                        <select name="EventID" class="form-control">
                        <?php while ($aRow = mysqli_fetch_array($rsEvents)): ?>
                            <option value="<?= $aRow['event_id']; ?>"><?= $aRow['event_title']; ?></option>
                        <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-7 col-xs-7">
                                <input type="submit" name="Submit" value="<?= gettext('Select Event'); ?>" class="btn btn-primary">
                            </div>
                            <div class="col-md-5 col-xs-5 text-right">
                                <a href="EventEditor.php"><?= gettext('Add New Event'); ?></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php

}
?>

<!-- Add Atendees Here -->
<?php
// If event is known, then show 2 text boxes, person being checked in and the person checking them in.
// Show a verify button and a button to add new visitor in dbase.
if (isset($_POST['Submit']) && isset($_POST['EventID']) || isset($_POST['Cancel'])) {
    $iEventID = FilterInput($_POST['EventID'], 'int');
    $sSQL = "SELECT * FROM events_event WHERE Event_id ='".$iEventID."';";
    $rsEvents = RunQuery($sSQL);
    $aRow = mysqli_fetch_array($rsEvents);
    extract($aRow); ?>
<form method="post" action="Checkin.php" name="Checkin">
	<input type="hidden" name="EventID" value="<?= $event_id; ?>">

    <div class="row">
        <div class="col-md-8 col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= gettext('Add Attendees for Event'); ?>: <?= $event_title; ?></h3>
            </div>
        
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <input type="textbox" class="form-control" name="child" placeholder="<?= gettext("Child's Number"); ?>">
                    </div>
                    
                    <div class="col-md-6 col-xs-6">
                        <input type="textbox" class="form-control" name="adult" placeholder="<?= gettext('Adult Number(Optional)'); ?>">
                    </div>
                </div>
            </div>
            
            <div class="box-footer text-center">
                <input type="submit" class="btn btn-primary" value="<?= gettext('Verify'); ?>" Name="Verify"
                onclick="javascript:document.location='Checkin.php';">
				<input type="submit" class="btn btn-default" value="<?= gettext('Back to Menu'); ?>" name="Exit"
                onClick="javascript:document.location='Checkin.php';">
				<input type="button" class="btn btn-primary" value="<?= gettext('Add Visitor'); ?>" name="Add"
                onClick="javascript:document.location='PersonEditor.php';">
            </div>
        </div>
        </div>
    </div>
</form>
<?php

}
//End Entry

//Verify Section - get the picture and name of both people.  Display Add or Cancel (back to add people)"
if (isset($_POST['EventID']) && isset($_POST['Verify']) && isset($_POST['child'])) {
    $iEventID = FilterInput($_POST['EventID'], 'int');
    $iChildID = FilterInput($_POST['child'], 'int');
    $iAdultID = FilterInput($_POST['adult'], 'int');

    $sSQL = "SELECT * FROM events_event WHERE Event_id ='".$iEventID."';";
    $rsEvents = RunQuery($sSQL);
    $aRow = mysqli_fetch_array($rsEvents);
    extract($aRow); ?>
<form method="post" action="Checkin.php" name="Checkin">
	<input type="hidden" name="EventID" value="<?= $event_id; ?>">
	<input type="hidden" name="child" value="<?= $iChildID; ?>">
	<input type="hidden" name="adult" value="<?= $iAdultID; ?>">
    
    <div class="row">
        <div class="col-md-8 col-xs-12">
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= gettext("Event"). ": " .$event_title ?></h3>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <div class="LightShadedBox text-center">
                        <?php
                            loadperson($iChildID); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-xs-6">
                        <div class="LightShadedBox text-center">
                        <?php
                            if ($iAdultID != null) {
                                loadperson($iAdultID);
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box-footer text-center">
                <input type="submit" class="btn btn-primary" value="<?= gettext('CheckIn'); ?>" name="CheckIn" 
                onClick="javascript:document.location='Checkin.php';">
                <input type="submit" class="btn btn-default" value="<?= gettext('Cancel'); ?>" name="Cancel"
                onClick="javascript:document.location='Checkin.php';">
            </div>
        </div>
        
        </div>
    </div>
</form>
<?php

}

// End Verify section.

// Checkin Section
if (isset($_POST['EventID']) && isset($_POST['child']) && (isset($_POST['CheckIn']) || isset($_POST['VerifyCheckOut']))) {
    //Fields -> event_id, person_id, checkin_date, checkin_id, checkout_date, checkout_id
    $iEventID = FilterInput($_POST['EventID'], 'int');
    $iChildID = FilterInput($_POST['child'], 'int');
    if (isset($_POST['CheckIn'])) {
        if ($_POST['adult'] != '') {
            $iCheckinID = FilterInput($_POST['adult'], 'int');
            $fields = '(event_id, person_id, checkin_date, checkin_id)';
            $values = "'".$iEventID."', '".$iChildID."', NOW(), '".$iCheckinID."'";
        } else {
            $fields = '(event_id, person_id, checkin_date)';
            $values = "'".$iEventID."', '".$iChildID."', NOW() ";
        }
        $sSQL = "INSERT IGNORE INTO event_attend $fields VALUES ( $values ) ;";
        RunQuery($sSQL);
    }
    if (isset($_POST['VerifyCheckOut'])) {
        if ($_POST['adult'] != '') {
            $iCheckoutID = FilterInput($_POST['adult'], 'int');
            $fields = 'checkout_date, checkout_id';
            $values = "checkout_date=NOW(), checkout_id='".$iCheckoutID."' ";
        } else {
            $fields = 'checkout_date';
            $values = 'checkout_date=NOW() ';
        }
        $sSQL = "UPDATE event_attend SET $values WHERE (person_id = '".$iChildID."' AND event_id='".$iEventID."') ;";
//        die($sSQL);
        RunQuery($sSQL);
    } ?>
	<form method="post" action="Checkin.php" name="Checkin">
        <input type="hidden" name="EventID" value="<?= $iEventID  ?>">
        <div class="form-group">
            <input type="submit" name="Submit" value="<?= gettext('Continue checkin') ?>" class="btn btn-primary">
        </div>
	</form>
<?php

}

//-- End checkin

//  Checkout section
if (isset($_POST['EventID']) && isset($_POST['Action']) && isset($_POST['child']) || isset($_POST['VerifyCheck'])) {
    $iEventID = FilterInput($_POST['EventID'], 'int');
    $iChildID = FilterInput($_POST['child'], 'int');

    $sSQL = "SELECT * FROM events_event WHERE Event_id ='".$iEventID."';";
    $rsEvents = RunQuery($sSQL);
    $aRow = mysqli_fetch_array($rsEvents);
    extract($aRow);

    if (isset($_POST['Action'])) {
        ?>
<form method="post" action="Checkin.php" name="Checkin">
    <input type="hidden" name="EventID" value="<?= $iEventID  ?>">
    <input type="hidden" name="child" value="<?= $iChildID  ?>">
    
    <div class="row">
        <div class="col-md-8 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= gettext("Event") .": ".$event_title ?></h3>
                </div>
                
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <div class="LightShadedBox text-center">
                                <?php
                                    loadperson($iChildID); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-xs-6">
                            <div class="form-group">
                                <label>Person Checking Out Child:</label>
                                <input type="textbox" name="adult" class="form-control" placeholder="(Number)">
                            </div>
                            
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="<?= gettext('Verify CheckOut') ?>" name="VerifyCheck"
                                onClick="javascript:document.location='Checkin.php';">
                                <input type="submit" class="btn btn-default" value="<?= gettext('Cancel') ?>" name="Cancel"
                                onClick="javascript:document.location='Checkin.php';">
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</form>
    <?php

    }
    
    if (isset($_POST['VerifyCheck'])) {
        $iAdultID = FilterInput($_POST['adult'], 'int'); ?>
<form method="post" action="Checkin.php" name="Checkin">
    <input type="hidden" name="EventID" value="<?= $iEventID ?>">
    <input type="hidden" name="child" value="<?= $iChildID ?>">
    <input type="hidden" name="adult" value="<?= $iAdultID ?>">
    
    <div class="row">
        <div class="col-md-8 col-xs-12">
        
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= gettext("Event"). ": ".$event_title ?></h3>
                </div>
                
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6 col-xs-6">
                                <div class="LightShadedBox text-center">
                                <?php
                                    loadperson($iChildID); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xs-6">
                                <div class="LightShadedBox text-center">
                                <?php
                                    loadperson($iAdultID); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-center">
                        <input type="submit" class="btn btn-primary" value="<?= gettext('Finalize CheckOut') ?>" name="VerifyCheckOut"
                        onClick="javascript:document.location='Checkin.php';">
                        <input type="submit" class="btn btn-default" value="<?= gettext('Cancel') ?>" name="Cancel"
                        onClick="javascript:document.location='Checkin.php';">
                    </div>
                </div>
            </div>
        
        </div>
    </div>
</form>
    <?php

    }
}
//End checkout
?>

<!-- ********************************************************************************************************** -->
<div class="box box-primary">
    <div class="box-body">
    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <td width="20%"><?= gettext('Name') ?></td>
                <td width="15%"><?= gettext('Checked In Time') ?></td>
                <td width="20%"><?= gettext('Checked In By') ?></td>
                <td width="15%"><?= gettext('Checked Out Time') ?></td>
                <td width="20%"><?= gettext('Checked Out By') ?></td>
                <td width="10%" nowrap><?= gettext('Action') ?></td>
            </tr>
        </thead>
        
        <tbody>
            
<?php
if (isset($_POST['EventID'])) {
    $EventID = FilterInput($_POST['EventID'], 'int');
    $sSQL = "SELECT * FROM event_attend WHERE event_id = '$EventID' ";                // ORDER BY person_id";
    $rsOpps = RunQuery($sSQL);
    $numAttRows = mysqli_num_rows($rsOpps);
    if ($numAttRows != 0) {
        $sRowClass = 'RowColorA';
        for ($na = 0; $na < $numAttRows; $na++) {
            $attRow = mysqli_fetch_array($rsOpps, MYSQLI_BOTH);
            extract($attRow);

            //Get Person who is checked in
            $sSQL = "SELECT * FROM person_per WHERE per_ID = $person_id ";
            $perOpps = RunQuery($sSQL);
            if (mysqli_num_rows($perOpps) > 0) {
                $perRow = mysqli_fetch_array($perOpps, MYSQLI_BOTH);
                extract($perRow);
                $sPerson = FormatFullName($per_Title, $per_FirstName, $per_MiddleName, $per_LastName, $per_Suffix, 3);
            } else {
                $sPerson = '';
            }
            $per_Title = '';
            $per_FirstName = '';
            $per_MiddleName = '';
            $per_LastName = '';
            $per_Suffix = '';

            //Get Person who checked person in
            if ($checkin_id > 0) {
                $sSQL = "SELECT * FROM person_per WHERE per_ID = $checkin_id";
                $perCheckin = RunQuery($sSQL);
                if (mysqli_num_rows($perCheckin) > 0) {
                    $perCheckinRow = mysqli_fetch_array($perCheckin, MYSQLI_BOTH);
                    extract($perCheckinRow);
                    $sCheckinby = FormatFullName($per_Title, $per_FirstName, $per_MiddleName, $per_LastName, $per_Suffix, 3);
                } else {
                    $sCheckinby = '';
                }
            } else {
                $sCheckinby = '';
            }
            $per_Title = '';
            $per_FirstName = '';
            $per_MiddleName = '';
            $per_LastName = '';
            $per_Suffix = '';

            //Get Person who checked person out
            if ($checkout_id > 0) {
                $sSQL = "SELECT * FROM person_per WHERE per_ID = $checkout_id";
                $perCheckout = RunQuery($sSQL);

                if (mysqli_num_rows($perCheckout) > 0) {
                    $perCheckoutRow = mysqli_fetch_array($perCheckout, MYSQLI_BOTH);
                    extract($perCheckoutRow);
                    $sCheckoutby = FormatFullName($per_Title, $per_FirstName, $per_MiddleName, $per_LastName, $per_Suffix, 3);
                } else {
                    $sCheckoutby = '';
                }
            } else {
                $sCheckoutby = '';
            }
            $per_Title = '';
            $per_FirstName = '';
            $per_MiddleName = '';
            $per_LastName = '';
            $per_Suffix = '';
            $sRowClass = AlternateRowStyle($sRowClass); ?>
            <tr class="<?= $sRowClass ?>">
                <td class="TextColumn"><?= $sPerson ?></td>
                <td class="TextColumn"><?= $checkin_date ?></td>
                <td class="TextColumn"><?= $sCheckinby ?></td>
                <td class="TextColumn"><?= $checkout_date ?></td>
                <td class="TextColumn"><?= $sCheckoutby ?></td>
                <td  class="TextColumn" align="center">

                <form method="POST" action="Checkin.php" name="DeletePersonFromEvent">
                  <input type="hidden" name="child" value="<?= $person_id ?>">
                  <input type="hidden" name="EventID" value="<?= $EventID ?>">
                  <input type="submit" name="Action" value="<?= gettext('CheckOut') ?>" class="btn btn-primary" >
                </form>
             </td>
            </tr>
        <?php

        }
    }
} else {
    echo '<tr><td colspan="6" align="center">' . gettext('No Attendees Assigned to Event') . '</td></tr>';
}
?>
            
        </tbody>
    </table>
    </div>
    </div>
</div>

<?php require 'Include/Footer.php';

function loadperson($iPersonID)
{
    if ($iPersonID == 0) {
        return;
    }

    $sSQL = 'SELECT a.*, family_fam.*, cls.lst_OptionName AS sClassName, fmr.lst_OptionName AS sFamRole, b.per_FirstName AS EnteredFirstName,
					b.Per_LastName AS EnteredLastName, c.per_FirstName AS EditedFirstName, c.per_LastName AS EditedLastName
				FROM person_per a
				LEFT JOIN family_fam ON a.per_fam_ID = family_fam.fam_ID
				LEFT JOIN list_lst cls ON a.per_cls_ID = cls.lst_OptionID AND cls.lst_ID = 1
				LEFT JOIN list_lst fmr ON a.per_fmr_ID = fmr.lst_OptionID AND fmr.lst_ID = 2
				LEFT JOIN person_per b ON a.per_EnteredBy = b.per_ID
				LEFT JOIN person_per c ON a.per_EditedBy = c.per_ID
				WHERE a.per_ID = '.$iPersonID;
    $rsPerson = RunQuery($sSQL);
    if ((!$rsPerson) || mysqli_num_rows($rsPerson) == 0) {
        return;
    }

    extract(mysqli_fetch_array($rsPerson));

    // Get the lists of custom person fields
    $sSQL = "SELECT person_custom_master.* FROM person_custom_master
				WHERE custom_Side = 'left' ORDER BY custom_Order";
    $rsLeftCustomFields = RunQuery($sSQL);

    $sSQL = "SELECT person_custom_master.* FROM person_custom_master
				WHERE custom_Side = 'right' ORDER BY custom_Order";
    $rsRightCustomFields = RunQuery($sSQL);

    // Get the custom field data for this person.
    $sSQL = 'SELECT * FROM person_custom WHERE per_ID = '.$iPersonID;
    $rsCustomData = RunQuery($sSQL);
    $aCustomData = mysqli_fetch_array($rsCustomData, MYSQLI_BOTH);

    // Get the notes for this person
    $sSQL = 'SELECT nte_Private, nte_ID, nte_Text, nte_DateEntered, nte_EnteredBy, nte_DateLastEdited, nte_EditedBy, a.per_FirstName AS EnteredFirstName, a.Per_LastName AS EnteredLastName, b.per_FirstName AS EditedFirstName, b.per_LastName AS EditedLastName ';
    $sSQL .= 'FROM note_nte ';
    $sSQL .= 'LEFT JOIN person_per a ON nte_EnteredBy = a.per_ID ';
    $sSQL .= 'LEFT JOIN person_per b ON nte_EditedBy = b.per_ID ';
    $sSQL .= 'WHERE nte_per_ID = '.$iPersonID;

    // Admins should see all notes, private or not.  Otherwise, only get notes marked non-private or private to the current user.
    if (!$_SESSION['bAdmin']) {
        $sSQL .= ' AND (nte_Private = 0 OR nte_Private = '.$_SESSION['iUserID'].')';
    }

    $rsNotes = RunQuery($sSQL);

    SelectWhichAddress($sAddress1, $sAddress2, $per_Address1, $per_Address2, $fam_Address1, $fam_Address2, false);
    $sAddress2 = SelectWhichInfo($per_Address2, $fam_Address2, false);
    $sCity = SelectWhichInfo($per_City, $fam_City, false);
    $sState = SelectWhichInfo($per_State, $fam_State, false);
    $sZip = SelectWhichInfo($per_Zip, $fam_Zip, false);
    $sCountry = SelectWhichInfo($per_Country, $fam_Country, false);

    echo '<font size="4"><b>';
    echo FormatFullName($per_Title, $per_FirstName, $per_MiddleName, $per_LastName, $per_Suffix, 0);
    echo '</font></b><br>';

    if ($fam_ID != '') {
        echo '<font size="2">(';
        if ($sFamRole != '') {
            echo $sFamRole;
        } else {
            echo gettext('Member');
        }
        echo gettext(' of the').' <a href="FamilyView.php?FamilyID='.$fam_ID.'">'.$fam_Name.'</a> '.gettext('family').' )</font><br><br>';
    } else {
        echo gettext('(No assigned family)').'<br><br>';
    }

    echo '<div class="TinyShadedBox">';
    echo '<font size="3">';
    if ($sAddress1 != '') {
        echo $sAddress1.'<br>';
    }
    if ($sAddress2 != '') {
        echo $sAddress2.'<br>';
    }
    if ($sCity != '') {
        echo $sCity.', ';
    }
    if ($sState != '') {
        echo $sState;
    }
    if ($sZip != '') {
        echo ' '.$sZip;
    }
    if ($sCountry != '') {
        echo '<br>'.$sCountry;
    }
    echo '</font>';
    echo '</div>';

        // Strip tags in case they were added for family inherited data
        $sAddress1 = strip_tags($sAddress1);
    $sCity = strip_tags($sCity);
    $sState = strip_tags($sState);
    $sCountry = strip_tags($sCountry);

        // Upload photo
        if (isset($_POST['UploadPhoto']) && ($_SESSION['bAddRecords'] || $bOkToEdit)) {
            if ($_FILES['Photo']['name'] == '') {
                $PhotoError = gettext('No photo selected for uploading.');
            } elseif ($_FILES['Photo']['type'] != 'image/pjpeg' && $_FILES['Photo']['type'] != 'image/jpeg') {
                $PhotoError = gettext('Only jpeg photos can be uploaded.');
            } else {
                // Create the thumbnail used by PersonView

            chmod($_FILES['Photo']['tmp_name'], 0777);

                $srcImage = imagecreatefromjpeg($_FILES['Photo']['tmp_name']);
                $src_w = imagesx($srcImage);
                $src_h = imagesy($srcImage);

                // Calculate thumbnail's height and width (a "maxpect" algorithm)
                $dst_max_w = 200;
                $dst_max_h = 350;
                if ($src_w > $dst_max_w) {
                    $thumb_w = $dst_max_w;
                    $thumb_h = $src_h * ($dst_max_w / $src_w);
                    if ($thumb_h > $dst_max_h) {
                        $thumb_h = $dst_max_h;
                        $thumb_w = $src_w * ($dst_max_h / $src_h);
                    }
                } elseif ($src_h > $dst_max_h) {
                    $thumb_h = $dst_max_h;
                    $thumb_w = $src_w * ($dst_max_h / $src_h);
                    if ($thumb_w > $dst_max_w) {
                        $thumb_w = $dst_max_w;
                        $thumb_h = $src_h * ($dst_max_w / $src_w);
                    }
                } else {
                    if ($src_w > $src_h) {
                        $thumb_w = $dst_max_w;
                        $thumb_h = $src_h * ($dst_max_w / $src_w);
                    } elseif ($src_w < $src_h) {
                        $thumb_h = $dst_max_h;
                        $thumb_w = $src_w * ($dst_max_h / $src_h);
                    } else {
                        if ($dst_max_w >= $dst_max_h) {
                            $thumb_w = $dst_max_h;
                            $thumb_h = $dst_max_h;
                        } else {
                            $thumb_w = $dst_max_w;
                            $thumb_h = $dst_max_w;
                        }
                    }
                }
                $dstImage = imagecreatetruecolor($thumb_w, $thumb_h);
                imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
                imagejpeg($dstImage, 'Images/Person/thumbnails/'.$iPersonID.'.jpg');
                imagedestroy($dstImage);
                imagedestroy($srcImage);
                move_uploaded_file($_FILES['Photo']['tmp_name'], 'Images/Person/'.$iPersonID.'.jpg');
            }
        } elseif (isset($_POST['DeletePhoto']) && $_SESSION['bDeleteRecords']) {
            unlink('Images/Person/'.$iPersonID.'.jpg');
            unlink('Images/Person/thumbnails/'.$iPersonID.'.jpg');
        }

        // Display photo or upload from file
        $photoFile = 'Images/Person/thumbnails/'.$iPersonID.'.jpg';
    if (file_exists($photoFile)) {
        echo '<a target="_blank" href="Images/Person/'.$iPersonID.'.jpg">';
        echo '<img border="1" src="'.$photoFile.'"></a>';
    } else {
        echo '<img border="0" src="Images/NoPhoto.png"><br><br><br>';
    }
}
?>
