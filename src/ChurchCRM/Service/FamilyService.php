<?php

namespace ChurchCRM\Service;

use ChurchCRM\dto\SystemURLs;
use Exception;

class FamilyService
{
    public function getViewURI($Id)
    {
        return SystemURLs::getRootPath().'/FamilyView.php?FamilyID='.$Id;
    }

    public function search($searchTerm)
    {
        global $cnInfoCentral;
        $fetch = 'SELECT fam_ID, fam_Name, fam_Address1, fam_City, fam_State FROM family_fam WHERE family_fam.fam_Name LIKE \'%'.$searchTerm.'%\' LIMIT 15';
        $result = mysqli_query($cnInfoCentral, $fetch);
        $families = [];
        while ($row = mysqli_fetch_array($result)) {
            $row_array['id'] = $row['fam_ID'];
            $row_array['familyName'] = $row['fam_Name'];
            $row_array['street'] = $row['fam_Address1'];
            $row_array['city'] = $row['fam_City'];
            $familyDisplayNameArray = [];
            array_push($familyDisplayNameArray, $row['fam_Name']);
            if ($row['fam_Address1'] != '') {
                array_push($familyDisplayNameArray, $row['fam_Address1']);
            }
            array_push($familyDisplayNameArray, $row['fam_City']);
            $row_array['displayName'] = implode(' - ', array_filter($familyDisplayNameArray));
            $row_array['uri'] = $this->getViewURI($row['fam_ID']);
            array_push($families, $row_array);
        }

        return $families;
    }

    public function lastEdited()
    {
        $sSQL = 'select * from family_fam order by fam_DateLastEdited desc  LIMIT 10;';
        $rsLastFamilies = RunQuery($sSQL);
        $families = [];
        while ($row = mysqli_fetch_array($rsLastFamilies)) {
            $row_array['id'] = $row['fam_ID'];
            $row_array['name'] = $row['fam_Name'];
            $row_array['address'] = $row['fam_Address1'];
            $row_array['city'] = $row['fam_City'];
            array_push($families, $row_array);
        }
        $this->returnFamilies($families);
    }

    public function getFamiliesJSON($families)
    {
        if ($families) {
            return '{"families": '.json_encode($families).'}';
        } else {
            return false;
        }
    }

    public function getFamilyPhoto($iFamilyID)
    {
        $photoFile = $this->getUploadedPhoto($iFamilyID);
        if ($photoFile != '') {
            return $photoFile;
        }

        return 'Images/Family/family-128.png';
    }

    public function getUploadedPhoto($iFamilyID)
    {
        $validExtensions = ['jpeg', 'jpg', 'png'];
        while (list(, $ext) = each($validExtensions)) {
            $photoFile = 'Images/Family/thumbnails/'.$iFamilyID.'.'.$ext;
            if (file_exists($photoFile)) {
                return $photoFile;
            }
        }

        return '';
    }

    public function getFamilyName($famID)
    {
        $sSQL = 'SELECT fam_ID, fam_Name, fam_Address1, fam_City, fam_State FROM family_fam WHERE fam_ID='.$famID;
        $rsFamilies = RunQuery($sSQL);
        $aRow = mysqli_fetch_array($rsFamilies);
        try {
            extract($aRow);
            $name = $fam_Name;
            if (isset($aHead[$fam_ID])) {
                $name .= ', '.$aHead[$fam_ID];
            }
            $name .= ' '.FormatAddressLine($fam_Address1, $fam_City, $fam_State);
        } catch (Exception $e) {
            $name = '';
        }

        return $name;
    }

    public function getFamilyStringByEnvelope($iEnvelope)
    {
        $sSQL = 'SELECT fam_ID, fam_Name, fam_Address1, fam_City, fam_State FROM family_fam WHERE fam_Envelope='.$iEnvelope;
        $rsFamilies = RunQuery($sSQL);
        $familyArray = [];
        while ($aRow = mysqli_fetch_array($rsFamilies)) {
            extract($aRow);
            $name = $this->getFamilyName($fam_ID);
            $familyArray = ['fam_ID' => $fam_ID, 'Name' => $name];
        }

        return json_encode($familyArray);
    }

    public function getFamilyStringByID($fam_ID)
    {
        $sSQL = 'SELECT fam_ID, fam_Name, fam_Address1, fam_City, fam_State FROM family_fam WHERE fam_ID='.$fam_ID;
        $rsFamilies = RunQuery($sSQL);
        $familyArray = [];
        while ($aRow = mysqli_fetch_array($rsFamilies)) {
            extract($aRow);
            $name = $fam_Name;
            if (isset($aHead[$fam_ID])) {
                $name .= ', '.$aHead[$fam_ID];
            }
            $name .= ' '.FormatAddressLine($fam_Address1, $fam_City, $fam_State);

            $familyArray = ['fam_ID' => $fam_ID, 'Name' => $name];
        }

        return json_encode($familyArray);
    }

    public function setFamilyCheckingAccountDetails($tScanString, $iFamily)
    {
        requireUserGroupMembership('bFinance');
    //Set the Routing and Account Number for a family
    $routeAndAccount = $micrObj->FindRouteAndAccount($tScanString); // use routing and account number for matching
    $sSQL = 'UPDATE family_fam SET fam_scanCheck="'.$routeAndAccount.'" WHERE fam_ID = '.$iFamily;
        RunQuery($sSQL);
    }
}
