<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CropAvatar
{

    private $src;
    private $data;
    private $dst;
    private $type;
    private $extension;
    private $msg;
    private $image_name;

    function __construct($src, $data, $file, $upload_path)
    {
        $this->setSrc($src, $upload_path);
        $this->setData($data);
        $this->setFile($file, $upload_path);
        $this->crop($this->src, $this->dst, $this->data);
    }

    private function setSrc($src, $upload_path)
    {
        if (!empty($src)) {
            $type = exif_imagetype($src);

            if ($type) {
                $this->src = $src;
                $this->type = $type;
                $this->extension = image_type_to_extension($type);
                $this->setDst($upload_path);
            }
        }
    }

    private function setData($data)
    {
        if (!empty($data)) {
            $this->data = json_decode(stripslashes($data));
        }
    }

    private function setFile($file, $upload_path)
    {
        $errorCode = $file['error'];

        if ($errorCode === UPLOAD_ERR_OK) {
            $type = exif_imagetype($file['tmp_name']);

            if ($type) {
                $extension = image_type_to_extension($type);
                $src = $upload_path . '.original' . $extension;
                //$full_path = base_url().$src;  
                if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {
                    if (file_exists($src)) {
                        unlink($src);
                    }
                    $result = move_uploaded_file($file['tmp_name'], $src);

                    if ($result) {
                        $this->src = $src;
                        $this->type = $type;
                        $this->extension = $extension;
                        $this->setDst($upload_path);
                    } else {
                        $this->msg = 'Failed to save file';
                    }
                } else {
                    $this->msg = 'Please upload image with the following types: JPG, PNG, GIF';
                }
            } else {
                $this->msg = 'Please upload image file';
            }
        } else {
            $this->msg = $this->codeToMessage($errorCode);
        }
    }

    private function setDst($upload_path)
    {
        $this->dst = $upload_path . '.png';
    }

    private function crop($src, $dst, $data)
    {
        if (!empty($src) && !empty($dst) && !empty($data)) {
            switch ($this->type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
            }

            if (!$src_img) {
                $this->msg = "Failed to read the image file";
                return;
            }

            $size = getimagesize($src);
            $size_w = $size[0]; // natural width
            $size_h = $size[1]; // natural height

            $src_img_w = $size_w;
            $src_img_h = $size_h;

            $degrees = $data->rotate;

            // Rotate the source image
            if (is_numeric($degrees) && $degrees != 0) {
                // PHP's degrees is opposite to CSS's degrees
                $new_img = imagerotate($src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127));

                imagedestroy($src_img);
                $src_img = $new_img;

                $deg = abs($degrees) % 180;
                $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

                $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
                $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

                // Fix rotated image miss 1px issue when degrees < 0
                $src_img_w -= 1;
                $src_img_h -= 1;
            }

            $tmp_img_w = $data->width;
            $tmp_img_h = $data->height;
            $dst_img_w = 220;
            $dst_img_h = 220;

            $src_x = $data->x;
            $src_y = $data->y;

            if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
                $src_x = $src_w = $dst_x = $dst_w = 0;
            } else if ($src_x <= 0) {
                $dst_x = -$src_x;
                $src_x = 0;
                $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
            } else if ($src_x <= $src_img_w) {
                $dst_x = 0;
                $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
            }

            if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
                $src_y = $src_h = $dst_y = $dst_h = 0;
            } else if ($src_y <= 0) {
                $dst_y = -$src_y;
                $src_y = 0;
                $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
            } else if ($src_y <= $src_img_h) {
                $dst_y = 0;
                $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
            }

            // Scale to destination position and size
            $ratio = $tmp_img_w / $dst_img_w;
            $dst_x /= $ratio;
            $dst_y /= $ratio;
            $dst_w /= $ratio;
            $dst_h /= $ratio;

            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

            // Add transparent background to destination image
            imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagesavealpha($dst_img, true);

            $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

            if ($result) {
                if (!imagepng($dst_img, $dst)) {
                    $this->msg = "Failed to save the cropped image file";
                }
            } else {
                $this->msg = "Failed to crop the image file";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    private function codeToMessage($code)
    {
        $errors = array(
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
        );

        if (array_key_exists($code, $errors)) {
            return $errors[$code];
        }

        return 'Unknown upload error';
    }

    public function getResult()
    {
        return !empty($this->data) ? $this->dst : $this->src;
    }

    public function getTemporery()
    {
        return !empty($this->data) ? base_url() . $this->dst : $this->src;
    }

    public function getMsg()
    {
        return $this->msg;
    }

}

class Company_users extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('company_users');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('company_users_model');
        //$this->mw_session['company_id'] = 2;
    }

    public function ajax_populate_company()
    {
        return $this->company_users_model->fetch_company_data($this->input->get());
    }

    public function ajax_populate_roles()
    {
        $Company_id = $this->input->post('data', true);
        $data['designationResult'] = $this->common_model->get_selected_values('designation', 'id,description', 'status=1 AND company_id=' . $Company_id);
        $data['roleResult'] = $this->common_model->get_selected_values('company_roles', 'arid,	rolename', 'status=1 AND company_id=' . $Company_id);
        $data['regionResult'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $Company_id);
        echo json_encode($data);
    }

    public function ajax_populate_country()
    {
        return $this->company_users_model->fetch_country_data($this->input->get());
    }

    public function ajax_populate_state()
    {
        return $this->company_users_model->fetch_state_data($this->input->get());
    }

    public function ajax_populate_city()
    {
        return $this->company_users_model->fetch_city_data($this->input->get());
    }

    public function index()
    {
        $data['module_id'] = '1.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->company_users_model->fetch_access_data();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', "status=1");
            $data['roleResult'] = array();
            $data['designationResult'] = array();
            $data['regionResult'] = array();
        } else {
            $data['designationResult'] = $this->common_model->get_selected_values('designation', 'id,description', 'status=1 AND company_id=' . $Company_id);
            $data['roleResult'] = $this->common_model->get_selected_values('company_roles', 'arid,	rolename', 'status=1 AND company_id=' . $Company_id);
            $data['regionResult'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('company_users/index', $data);
    }

    public function create()
    {
        $data['module_id'] = '1.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('company_users');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', "status=1");
            $data['roleResult'] = array();
            $data['designationResult'] = array();
            $data['regionResult'] = array();
        } else {
            $data['designationResult'] = $this->common_model->get_selected_values('designation', 'id,description', 'status=1 AND company_id=' . $Company_id);
            $data['roleResult'] = $this->common_model->get_selected_values('company_roles', 'arid,rolename', 'status=1 AND company_id=' . $Company_id);
            $data['regionResult'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $Company_id);
        }
        $data['LoginType'] = $this->common_model->fetch_object_by_field('login_type', 'status', '1');
        $this->load->view('company_users/create', $data);
    }

    public function edit($id, $Step = 1)
    {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('company_users');
            return;
        }
        $data['module_id'] = '1.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->company_users_model->fetch_user($user_id);
        $data['Step'] = $Step;
        $Company_id = $this->mw_session['company_id'];

        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', "status=1");
            $Base_url = base_url();
        } else {
            $Base_url = $this->config->item('assets_url');
        }
        if ($data['result']->avatar != "") {
            $data['avatar_url'] = $Base_url . 'assets/uploads/avatar/' . $data['result']->avatar;
        } else {
            $data['avatar_url'] = $Base_url . 'assets/uploads/avatar/no-avatar.jpg';
        }
        $data['LoginType'] = $this->common_model->fetch_object_by_field('login_type', 'status', '1');
        $data['DesignationResult'] = $this->common_model->get_selected_values('designation', 'id,description', 'status=1 AND company_id=' . $data['result']->company_id);
        $data['Role'] = $this->common_model->get_selected_values('company_roles', 'arid,rolename', 'status=1 AND company_id=' . $data['result']->company_id);
        $data['TrainerRegionSet'] = $this->company_users_model->getTrainerRegionRightset($data['result']->company_id, $user_id);
        $data['Company_id'] = $this->mw_session['company_id'];
        $Html = "";
        $WRightsRow = 1;
        if ($data['result']->workshoprights_type == 2) {
            $WorkshopRegionSet = $this->common_model->get_selected_values('cmsusers_wregion_rights', 'id,region_id', "userid=" . $user_id);
            if (count((array) $WorkshopRegionSet) > 0) {
                $RegionRowset = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $data['result']->company_id);
                $WorkshopTypeset = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 AND company_id=' . $data['result']->company_id);
                foreach ($WorkshopRegionSet as $regionSet) {
                    $Html .= '<tr id="Row_' . $WRightsRow . '">';
                    $Html .= '<td><select id="WRegion_id' . $WRightsRow . '" name="Wregion_id[]" class="form-control input-sm select2"  style="width:100%" onchange="LoadCustomWorkshop(' . $WRightsRow . ');">';
                    $Html .= '<option value="">Please Select</option>';
                    foreach ($RegionRowset as $tr) {
                        $Html .= '<option value="' . $tr->id . '" ' . ($tr->id == $regionSet->region_id ? 'Selected' : '') . '>' . $tr->region_name . '</option>';
                    }
                    $Html .= '</select></td>';
                    $Html .= '<input type="hidden" value="' . $WRightsRow . '" name="TotalRow[]">';
                    $Html .= '<td><select id="Workshop_type_id' . $WRightsRow . '" name="Workshop_type_id' . $WRightsRow . '[]" class="form-control input-sm select2 custClass ValueUnq" multiple="" style="width:100%" onchange="LoadCustomWorkshop(' . $WRightsRow . ');">';
                    $Html .= '';
                    $SelectedWorkshopSet = $this->common_model->get_selected_values('cmsusers_wtype_rights', 'workshop_type_id', ' userid=' . $user_id . ' AND rights_id=' . $regionSet->id);
                    $SelectedTypeArray = [];
                    if (count((array) $SelectedWorkshopSet) > 0) {
                        foreach ($SelectedWorkshopSet as $value) {
                            $SelectedTypeArray[] = $value->workshop_type_id;
                        }
                    }
                    foreach ($WorkshopTypeset as $tp) {
                        $Html .= '<option value="' . $tp->id . '" ' . (in_array($tp->id, $SelectedTypeArray) ? 'Selected' : '') . ' >' . $tp->workshop_type . '</option>';
                    }
                    $Html .= '</select></td>';

                    $Html .= '<td><select id="Workshop_id' . $WRightsRow . '" name="Workshop_id' . $WRightsRow . '[]" class="form-control input-sm select2 custClass" style="width:100%" multiple="" >';
                    $SelectedWorkshopSet = $this->common_model->get_selected_values('cmsusers_workshop_rights', 'workshop_id', ' userid=' . $user_id . ' AND rights_id=' . $regionSet->id);
                    $SeletedWokshopArray = [];
                    if (count((array) $SelectedWorkshopSet) > 0) {
                        foreach ($SelectedWorkshopSet as $value) {
                            $SeletedWokshopArray[] = $value->workshop_id;
                        }
                    }

                    $Workshopset = $this->common_model->get_selected_values('workshop', 'id,workshop_name', 'status=1 AND region=' . $regionSet->region_id);

                    if (count((array) $Workshopset) > 0) {
                        foreach ($Workshopset as $tp) {
                            $Html .= '<option value="' . $tp->id . '" ' . (in_array($tp->id, $SeletedWokshopArray) ? 'Selected' : '') . ' >' . $tp->workshop_name . '</option>';
                        }
                    }
                    $Html .= '</select></td>';
                    $Html .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $WRightsRow . ')";><i class="fa fa-times"></i></button> </td></tr>';

                    $WRightsRow++;
                }
            }
        }
        $data['TotalWRow'] = $WRightsRow;
        $data['WorkshophtmlData'] = $Html;
        $Html = "";
        $TRightsRow = 1;
        if ($data['result']->userrights_type == 2) {
            $TrainerRegionSet = $this->common_model->get_selected_values('cmsusers_tregion_rights', 'id,region_id', "userid=" . $user_id);
            if (count((array) $TrainerRegionSet) > 0) {
                $RegionRowset = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $data['result']->company_id);
                foreach ($TrainerRegionSet as $Regionset) {
                    $Html .= '<tr id="TRow_' . $TRightsRow . '">';
                    $Html .= '<td><select id="Tregion_id' . $TRightsRow . '" name="Tregion_id[]" class="form-control input-sm select2"  style="width:100%" onchange="LoadCustomTrainer(' . $TRightsRow . ');">';
                    $Html .= '<option value="">Please Select</option>';
                    foreach ($RegionRowset as $tr) {
                        $Html .= '<option value="' . $tr->id . '" ' . ($tr->id == $Regionset->region_id ? 'Selected' : '') . '>' . $tr->region_name . '</option>';
                    }
                    $Html .= '</select></td>';
                    $Html .= '<input type="hidden" value="' . $TRightsRow . '" name="TotalRow[]">';
                    $Html .= '<td><select id="TRTrainer_id' . $TRightsRow . '" name="TRTrainer_id' . $TRightsRow . '[]" class="form-control input-sm select2 custClass" style="width:100%" multiple="" >';
                    $SelectedTrainerSet = $this->common_model->get_selected_values('cmsusers_rights', 'rightsuser_id', ' userid=' . $user_id . ' AND rights_id=' . $Regionset->id);
                    $SeletedTrainerArray = [];
                    if (count((array) $SelectedTrainerSet) > 0) {
                        foreach ($SelectedTrainerSet as $value) {
                            $SeletedTrainerArray[] = $value->rightsuser_id;
                        }
                    }
                    $WhereCond = "region_id=" . $Regionset->region_id . " AND  userid !=" . $user_id . " AND status=1 AND company_id=" . $data['result']->company_id;
                    $TrainerList = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name," ",last_name) as name', $WhereCond);
                    if (count((array) $TrainerList) > 0) {
                        foreach ($TrainerList as $tp) {
                            $Html .= '<option value="' . $tp->userid . '" ' . (in_array($tp->userid, $SeletedTrainerArray) ? 'Selected' : '') . ' >' . $tp->name . '</option>';
                        }
                    }
                    $Html .= '</select></td>';
                    $Html .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="TrainerRowDelete(' . $TRightsRow . ')";><i class="fa fa-times"></i></button> </td></tr>';
                }
                $TRightsRow++;
            }
        }
        $data['TotalTRow'] = $TRightsRow;
        $data['TrainerhtmlData'] = $Html;
        $this->load->view('company_users/edit', $data);
    }

    public function copy($id)
    {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('company_users');
            return;
        }
        $data['module_id'] = '1.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->company_users_model->fetch_user($user_id);
        $data['CompanySet'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        $data['LoginType'] = $this->common_model->fetch_object_by_field('login_type', 'status', '1');
        $data['DesignationResult'] = $this->common_model->fetch_object_by_field('designation', 'company_id', $data['result']->company_id);
        $data['Role'] = $this->common_model->fetch_object_by_field('company_roles', 'company_id', $data['result']->company_id);
        $data['RegionResult'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $data['result']->company_id);
        $this->load->view('company_users/copy', $data);
    }

    public function view($id)
    {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('company_users');
            return;
        }

        $data['module_id'] = '1.03';
        $data['result'] = $this->company_users_model->fetch_user($user_id);

        $data['LoginType'] = $this->common_model->fetch_object_by_field('login_type', 'status', '1');
        $data['DesignationResult'] = $this->common_model->get_selected_values('designation', 'id,description', 'status=1 AND company_id=' . $data['result']->company_id);
        $data['Role'] = $this->common_model->get_selected_values('company_roles', 'arid,	rolename', 'status=1 AND company_id=' . $data['result']->company_id);
        $data['TrainerRegionSet'] = $this->company_users_model->getTrainerRegionRightset($data['result']->company_id, $user_id);
        $data['Company_id'] = $this->mw_session['company_id'];
        if ($data['Company_id'] == "") {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', "status=1");
            $Base_url = base_url();
        } else {
            $Base_url = $this->config->item('assets_url');
        }
        if ($data['result']->avatar != "") {
            $data['avatar_url'] = $Base_url . 'assets/uploads/avatar/' . $data['result']->avatar;
        } else {
            $data['avatar_url'] = $Base_url . 'assets/uploads/avatar/no-profile.jpg';
        }
        $Html = "";
        $WRightsRow = 1;
        if ($data['result']->workshoprights_type == 2) {
            $WorkshopRegionSet = $this->common_model->get_selected_values('cmsusers_wregion_rights', 'id,region_id', "userid=" . $user_id);
            if (count((array) $WorkshopRegionSet) > 0) {
                $RegionRowset = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $data['result']->company_id);
                $WorkshopTypeset = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 AND company_id=' . $data['result']->company_id);
                foreach ($WorkshopRegionSet as $regionSet) {
                    $Html .= '<tr id="Row_' . $WRightsRow . '">';
                    $Html .= '<td><select id="WRegion_id' . $WRightsRow . '" name="Wregion_id[]" class="form-control input-sm select2"  style="width:100%" disabled>';
                    $Html .= '<option value="">Please Select</option>';
                    foreach ($RegionRowset as $tr) {
                        $Html .= '<option value="' . $tr->id . '" ' . ($tr->id == $regionSet->region_id ? 'Selected' : '') . '>' . $tr->region_name . '</option>';
                    }
                    $Html .= '</select></td>';
                    $Html .= '<input type="hidden" value="' . $WRightsRow . '" name="TotalRow[]">';
                    $Html .= '<td><select id="Workshop_type_id' . $WRightsRow . '" name="Workshop_type_id' . $WRightsRow . '[]" class="form-control input-sm select2 custClass ValueUnq" multiple="" style="width:100%" disabled>';
                    $Html .= '';
                    $SelectedWorkshopSet = $this->common_model->get_selected_values('cmsusers_wtype_rights', 'workshop_type_id', ' userid=' . $user_id . ' AND rights_id=' . $regionSet->id);
                    $SelectedTypeArray = [];
                    if (count((array) $SelectedWorkshopSet) > 0) {
                        foreach ($SelectedWorkshopSet as $value) {
                            $SelectedTypeArray[] = $value->workshop_type_id;
                        }
                    }
                    foreach ($WorkshopTypeset as $tp) {
                        $Html .= '<option value="' . $tp->id . '" ' . (in_array($tp->id, $SelectedTypeArray) ? 'Selected' : '') . ' >' . $tp->workshop_type . '</option>';
                    }
                    $Html .= '</select></td>';

                    $Html .= '<td><select id="Workshop_id' . $WRightsRow . '" name="Workshop_id' . $WRightsRow . '[]" class="form-control input-sm select2 custClass" style="width:100%" multiple="" disabled>';
                    $SelectedWorkshopSet = $this->common_model->get_selected_values('cmsusers_workshop_rights', 'workshop_id', ' userid=' . $user_id . ' AND rights_id=' . $regionSet->id);
                    $SeletedWokshopArray = [];
                    if (count((array) $SelectedWorkshopSet) > 0) {
                        foreach ($SelectedWorkshopSet as $value) {
                            $SeletedWokshopArray[] = $value->workshop_id;
                        }
                    }

                    $Workshopset = $this->common_model->get_selected_values('workshop', 'id,workshop_name', 'status=1 AND region=' . $regionSet->region_id);

                    if (count((array) $Workshopset) > 0) {
                        foreach ($Workshopset as $tp) {
                            $Html .= '<option value="' . $tp->id . '" ' . (in_array($tp->id, $SeletedWokshopArray) ? 'Selected' : '') . ' >' . $tp->workshop_name . '</option>';
                        }
                    }
                    $Html .= '</select></td>';
                    $Html .= '</tr>';

                    $WRightsRow++;
                }
            }
        }
        $data['TotalWRow'] = $WRightsRow;
        $data['WorkshophtmlData'] = $Html;
        $Html = "";
        $TRightsRow = 1;
        if ($data['result']->userrights_type == 2) {
            $TrainerRegionSet = $this->common_model->get_selected_values('cmsusers_tregion_rights', 'id,region_id', "userid=" . $user_id);
            if (count((array) $TrainerRegionSet) > 0) {
                $RegionRowset = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $data['result']->company_id);
                foreach ($TrainerRegionSet as $Regionset) {
                    $Html .= '<tr id="TRow_' . $TRightsRow . '">';
                    $Html .= '<td><select id="Tregion_id' . $TRightsRow . '" name="Tregion_id[]" class="form-control input-sm select2"  style="width:100%" disabled>';
                    $Html .= '<option value="">Please Select</option>';
                    foreach ($RegionRowset as $tr) {
                        $Html .= '<option value="' . $tr->id . '" ' . ($tr->id == $Regionset->region_id ? 'Selected' : '') . '>' . $tr->region_name . '</option>';
                    }
                    $Html .= '</select></td>';
                    $Html .= '<input type="hidden" value="' . $TRightsRow . '" name="TotalRow[]">';
                    $Html .= '<td><select id="TRTrainer_id' . $TRightsRow . '" name="TRTrainer_id' . $TRightsRow . '[]" class="form-control input-sm select2 custClass" style="width:100%" multiple="" disabled>';
                    $SelectedTrainerSet = $this->common_model->get_selected_values('cmsusers_rights', 'rightsuser_id', ' userid=' . $user_id . ' AND rights_id=' . $Regionset->id);
                    $SeletedTrainerArray = [];
                    if (count((array) $SelectedTrainerSet) > 0) {
                        foreach ($SelectedTrainerSet as $value) {
                            $SeletedTrainerArray[] = $value->rightsuser_id;
                        }
                    }
                    $WhereCond = "region_id=" . $Regionset->region_id . " AND  userid !=" . $user_id . " AND status=1 AND company_id=" . $data['result']->company_id;
                    $TrainerList = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name," ",last_name) as name', $WhereCond);
                    if (count((array) $TrainerList) > 0) {
                        foreach ($TrainerList as $tp) {
                            $Html .= '<option value="' . $tp->userid . '" ' . (in_array($tp->userid, $SeletedTrainerArray) ? 'Selected' : '') . ' >' . $tp->name . '</option>';
                        }
                    }
                    $Html .= '</select></td>';
                    $Html .= '</tr>';
                }
                $TRightsRow++;
            }
        }
        $data['TotalTRow'] = $TRightsRow;
        $data['TrainerhtmlData'] = $Html;
        $this->load->view('company_users/view', $data);
    }

    public function userrights($id, $Module_No)
    {
        //$Editid = base64_decode($id);
        $data['Editid'] = $id;
        if ($Module_No == 1) {
            $this->load->view('company_users/userlist', $data);
        } elseif ($Module_No == 3) {
            $this->load->view('company_users/regionlist', $data);
        } elseif ($Module_No == 2) {
            $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1');
            $data['Region'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1');
            $this->load->view('company_users/workshoplist', $data);
        }
    }

    public function addWorkshopRights($Module)
    {
        $NewUsersArrray = $this->input->post('NewWorkshopArray');
        $aaData = array();
        if (count((array) $NewUsersArrray) > 0) {
            $ArrayList = implode(',', $NewUsersArrray);
            $dtWhere = " WHERE u.id IN($ArrayList)";
            if ($Module == 3) {
                $aHeader = array('id', 'workshop_name', 'start_date', 'end_date', 'Actions');
                $DTRenderArray = $this->company_users_model->getUserWorkshopData($dtWhere);
            } else {
                $aHeader = array('id', 'region_name', 'totalWorkshop', 'Actions');
                $DTRenderArray = $this->company_users_model->getUserRegionWorkshopData($dtWhere);
            }
            $TotalHeader = count((array) $aHeader);

            foreach ($DTRenderArray['ResultSet'] as $key => $dtRow) {
                $aatData = array();
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($aHeader[$i] == "Actions") {
                        $row = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete"><i class="fa fa-times"></i></button>';
                    } else {
                        $row = $dtRow[$aHeader[$i]];
                    }
                    array_push($aatData, $row);
                }
                $aaData[] = $aatData;
            }
        }
        echo json_encode($aaData);
    }

    public function addUsersRights()
    {
        $NewUsersArrray = $this->input->post('NewUsersArrray');
        if (count((array) $NewUsersArrray) > 0) {
            $aHeader = array('userid', 'name', 'email', 'designation', 'Actions');
            $UserList = implode(',', $NewUsersArrray);
            $TotalHeader = count((array) $aHeader);
            $dtWhere = " WHERE userid IN($UserList)";
            $DTRenderArray = $this->company_users_model->getUserrightsData($dtWhere);

            foreach ($DTRenderArray['ResultSet'] as $key => $dtRow) {
                $aatData = array();
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($aHeader[$i] == "Actions") {
                        $row = '<button type="button" id="remove" value="' . $dtRow['userid'] . '" name="remove"  class="btn btn-danger btn-sm delete"><i class="fa fa-times"></i></button>';
                    } else {
                        $row = $dtRow[$aHeader[$i]];
                    }
                    array_push($aatData, $row);
                }
                $aaData[] = $aatData;
            }
        }
        echo json_encode($aaData);
    }

    public function UpdateUserRights($Module, $id)
    {
        $Editid = base64_decode($id);
        $Success = 1;
        $Msg = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Msg = "You have no rights to edit,Contact Administrator for rights";
            $Success = 0;
        } else {
            $now = date('Y-m-d H:i:s');
            if ($Module == 1) {
                $Options = $this->input->post('userrights_type');
                if ($Options == 2) {
                    $regionArray = $this->input->post('Tregion_id');
                    if (count((array) $regionArray) > 0) {
                        $ArrayCountArray = array_count_values($regionArray);
                        foreach ($ArrayCountArray as $key => $value) {
                            if ($key == null) {
                                $Msg .= "Region field is required.<br/>";
                                $Success = 0;
                            } elseif ($value > 1) {
                                $Success = 0;
                                $RegionData = $this->common_model->get_value("region", "region_name", "id=" . $key);
                                $Msg .= "Same  '" . $RegionData->region_name . "' Region are selected<br/>";
                            }
                        }
                    }
                    if ($Success) {
                        $this->common_model->delete('cmsusers_rights', 'userid', $Editid);
                        $this->common_model->delete('cmsusers_tregion_rights', 'userid', $Editid);
                        $this->common_model->delete('temp_trights', 'user_id', $Editid);
                        if (count((array) $regionArray) > 0) {
                            foreach ($regionArray as $key => $value) {
                                $RowNo = $this->input->post('TotalRow')[$key];
                                if ($value == "") {
                                    continue;
                                }
                                $UData = array(
                                    'userid' => $Editid,
                                    'region_id' => $value,
                                    'addeddate' => $now,
                                    'addedby' => $this->mw_session['user_id']
                                );
                                $rights_id = $this->common_model->insert('cmsusers_tregion_rights', $UData);
                                $Trainer_id = $this->input->post('TRTrainer_id' . $RowNo);
                                if (isset($Trainer_id) && count((array) $Trainer_id) > 0) {
                                    foreach ($Trainer_id as $key => $value2) {
                                        $UData = array(
                                            'userid' => $Editid,
                                            'rights_id' => $rights_id,
                                            'rightsuser_id' => $value2
                                        );
                                        $TinsertId = $this->common_model->insert('cmsusers_rights', $UData);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $this->common_model->delete('cmsusers_rights', 'userid', $Editid);
                    $this->common_model->delete('cmsusers_tregion_rights', 'userid', $Editid);
                    $this->common_model->delete('temp_trights', 'user_id', $Editid);
                }
                if ($Success) {
                    $CurrentUserData = $this->common_model->get_value('company_users', 'userrights_type', 'userid=' . $Editid);
                    if ($CurrentUserData->userrights_type != $Options) {
                        $UData = array(
                            'userrights_type' => $Options,
                            'modifieddate' => $now,
                            'modifiedby' => $this->mw_session['user_id']
                        );
                        $this->common_model->update('company_users', 'userid', $Editid, $UData);
                    }
                    $Msg = "User rights change successfully.!";
                }
            } else {
                $Options = $this->input->post('workshoprights_type');
                if ($Options == 2) {
                    $regionArray = $this->input->post('Wregion_id');
                    if (count((array) $regionArray) > 0) {
                        $ArrayCountArray = array_count_values($regionArray);
                        foreach ($ArrayCountArray as $key => $value) {
                            if ($key == null) {
                                $Msg .= "Region field is required.<br/>";
                                $Success = 0;
                            } elseif ($value > 1) {
                                $Success = 0;
                                $RegionData = $this->common_model->get_value("region", "region_name", "id=" . $key);
                                $Msg .= "Same  '" . $RegionData->region_name . "' Region are selected<br/>";
                            }
                        }
                    }
                    if ($Success) {
                        $this->common_model->delete_whereclause("cmsusers_workshop_rights", " userid=" . $Editid);
                        $this->common_model->delete_whereclause("cmsusers_wregion_rights", " userid=" . $Editid);
                        $this->common_model->delete_whereclause("cmsusers_wtype_rights", " userid=" . $Editid);
                        if (count((array) $regionArray) > 0) {
                            foreach ($regionArray as $key => $value) {
                                $RowNo = $this->input->post('TotalRow')[$key];
                                if ($value == "") {
                                    continue;
                                }
                                $Workshop_type = $this->input->post('Workshop_type_id' . $RowNo);
                                $UData = array(
                                    'userid' => $Editid,
                                    'region_id' => $value,
                                    'addeddate' => $now,
                                    'all_flag' => (count((array) $Workshop_type) > 0 ? 0 : 1),
                                    'addedby' => $this->mw_session['user_id']
                                );
                                $rights_id = $this->common_model->insert('cmsusers_wregion_rights', $UData);
                                $Workshop_id = $this->input->post('Workshop_id' . $RowNo);
                                if (isset($Workshop_type) && count((array) $Workshop_type) > 0) {
                                    $TempList = "";
                                    foreach ($Workshop_type as $value) {
                                        $UData = array(
                                            'userid' => $Editid,
                                            'rights_id' => $rights_id,
                                            'all_flag' => (count((array) $Workshop_id) > 0 ? 0 : 1),
                                            'workshop_type_id' => $value
                                        );
                                        $TinsertId = $this->common_model->insert('cmsusers_wtype_rights', $UData);
                                    }
                                }
                                if (isset($Workshop_id) && count((array) $Workshop_id) > 0) {
                                    $TempList = "";
                                    foreach ($Workshop_id as $key => $value) {
                                        $UData = array(
                                            'userid' => $Editid,
                                            'rights_id' => $rights_id,
                                            'workshop_id' => $value
                                        );
                                        $TinsertId = $this->common_model->insert('cmsusers_workshop_rights', $UData);
                                    }
                                }
                            }
                        }
                        $this->common_model->SyncWorkshopRights($Editid, 1);
                    }
                } else {
                    $this->common_model->delete_whereclause("cmsusers_workshop_rights", " userid=" . $Editid);
                    $this->common_model->delete_whereclause("cmsusers_wregion_rights", " userid=" . $Editid);
                    $this->common_model->delete_whereclause("cmsusers_wtype_rights", " userid=" . $Editid);
                    $this->common_model->delete_whereclause("temp_wrights", " user_id=" . $Editid);
                }
                if ($Success) {
                    $Msg = "User rights change successfully.!";
                    $CurrentUserData = $this->common_model->get_value('company_users', 'workshoprights_type', 'userid=' . $Editid);
                    if ($CurrentUserData->workshoprights_type != $Options) {
                        $UData = array(
                            'workshoprights_type' => $Options,
                            'modifieddate' => $now,
                            'modifiedby' => $this->mw_session['user_id']
                        );
                        $this->common_model->update('company_users', 'userid', $Editid, $UData);
                    }
                }
            }
        }
        $data['Msg'] = $Msg;
        $data['Success'] = $Success;
        echo json_encode($data);
    }

    public function RemoveUsersRights($id)
    {
        $Editid = base64_decode($id);
        $Success = 1;
        $Msg = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Msg = "You have no rights to edit,Contact Administrator for rights";
            $Success = 0;
        } else {
            $Remove_id = $this->input->post('Remove_id');
            $Module = $this->input->post('Module');
            if ($Module == 1) {
                $this->common_model->delete_whereclause('cmsusers_rights', 'userid=' . $Editid . " AND rightsuser_id=" . $Remove_id);
                $Msg = "User has been removed.!";
            } elseif ($Module == 2) {
                $this->common_model->delete_whereclause('cmsusers_workshop_rights', 'userid=' . $Editid . " AND workshop_id=" . $Remove_id);
                $Msg = "Selected workshop has been removed.!";
            } elseif ($Module == 3) {
                $this->common_model->delete_whereclause('cmsusers_wregion_rights', 'userid=' . $Editid . " AND region_id=" . $Remove_id);
                $Msg = "Selected Region has been removed.!";
            }
        }
        $data['Msg'] = $Msg;
        $data['Success'] = $Success;
        echo json_encode($data);
    }

    public function RegionRights_NewSearch($id)
    {
        $Editid = base64_decode($id);
        $dtSearchColumns = array('id', 'region_name');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $CurrnetUserData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $Editid);
        if ($CurrnetUserData->company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1";
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1";
            }
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.id NOT IN (SELECT region_id FROM cmsusers_wregion_rights where userid  = " . $Editid . ")";
        } else {
            $dtWhere .= " WHERE u.id NOT IN (SELECT region_id FROM cmsusers_wregion_rights where userid  = " . $Editid . ")";
        }
        $DTRenderArray = $this->company_users_model->getUserRegionWorkshopData($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $AlreadySelected = explode(',', $this->input->get('WorkshopArray'));
        $dtDisplayColumns = array('id', 'region_name', 'totalWorkshop', 'Actions');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="id[]" id="chk' . $dtRow['id'] . '" ';
                    if (count((array) $AlreadySelected) > 0 && in_array($dtRow['id'], $AlreadySelected)) {
                        $action .= ' checked disabled ';
                    }
                    $action .= 'value="' . $dtRow['id'] . '" onclick="SelectedWorkshop(' . $dtRow['id'] . ')"/>
                                <span></span>
                            </label>';
                    $row[] = $action;
                } else {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function WorkshopRights_NewSearch($id)
    {
        $Editid = base64_decode($id);
        $dtSearchColumns = array('id', 'workshop_name', 'start_date', 'end_date');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $CurrnetUserData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $Editid);
        if ($CurrnetUserData->company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $CurrnetUserData->company_id;
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $CurrnetUserData->company_id;
            }
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.id NOT IN (SELECT workshop_id FROM cmsusers_workshop_rights where userid  = " . $Editid . ")";
        } else {
            $dtWhere .= " WHERE u.id NOT IN (SELECT workshop_id FROM cmsusers_workshop_rights where userid  = " . $Editid . ")";
        }
        $Start_date = ($this->input->get('start_date') ? $this->input->get('start_date') : '');
        $End_date = ($this->input->get('end_date') ? $this->input->get('end_date') : '');
        if ($Start_date != "") {
            $dtWhere .= " AND u.start_date between '" . date('Y-m-d', strtotime($Start_date)) .
                "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
        }
        $wktype = ($this->input->get('wktype') ? $this->input->get('wktype') : '');
        if ($wktype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.workshop_type  = " . $wktype;
            }
        }
        $region = ($this->input->get('region') ? $this->input->get('region') : '');
        if ($region != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.region  = " . $region;
            }
        }
        $DTRenderArray = $this->company_users_model->getUserWorkshopData($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $AlreadySelected = explode(',', $this->input->get('WorkshopArray'));
        $dtDisplayColumns = array('id', 'workshop_name', 'start_date', 'end_date', 'Actions');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="id[]" id="chk' . $dtRow['id'] . '" ';
                    if (count((array) $AlreadySelected) > 0 && in_array($dtRow['id'], $AlreadySelected)) {
                        $action .= ' checked disabled ';
                    }
                    $action .= 'value="' . $dtRow['id'] . '" onclick="SelectedWorkshop(' . $dtRow['id'] . ')"/>
                                <span></span>
                            </label>';
                    $row[] = $action;
                } else {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function UserRights_NewSearch($id)
    {
        $Editid = base64_decode($id);
        $dtSearchColumns = array('userid', 'u.first_name', 'u.email', 'd.description', 'u.last_name', 'u.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $CurrnetUserData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $Editid);
        if ($CurrnetUserData->company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $CurrnetUserData->company_id;
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $CurrnetUserData->company_id;
            }
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND userid NOT IN (SELECT rightsuser_id FROM cmsusers_rights where userid  = " . $Editid . ") AND userid NOT IN($Editid)";
        } else {
            $dtWhere .= " WHERE userid NOT IN (SELECT rightsuser_id FROM cmsusers_rights where userid  = " . $Editid . ") AND userid NOT IN($Editid)";
        }
        $DTRenderArray = $this->company_users_model->getUserrightsData($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $AlreadySelected = explode(',', $this->input->get('MainUsersArray'));

        $dtDisplayColumns = array('userid', 'name', 'email', 'designation', 'Actions');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="id[]" id="chk' . $dtRow['userid'] . '" ';
                    if (count((array) $AlreadySelected) > 0 && in_array($dtRow['userid'], $AlreadySelected)) {
                        $action .= ' checked disabled ';
                    }
                    $action .= 'value="' . $dtRow['userid'] . '" onclick="SelectedUsers(' . $dtRow['userid'] . ')"/>
                                <span></span>
                            </label>';
                    $row[] = $action;
                } else {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function UserRights_tableRefresh($id)
    {
        $Editid = base64_decode($id);
        $dtSearchColumns = array('u.userid', 'u.userid', 'r.region_name', 'u.first_name', 'u.email', 'd.description', 'u.last_name');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $CurrnetUserData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $Editid);
        if ($CurrnetUserData->company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1 AND u.userid !=" . $Editid;
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1 AND u.userid !=" . $Editid;
            }
        }
        $CheckedValue = ($this->input->get('CheckedValue') ? $this->input->get('CheckedValue') : '');
        $DedaultRights = false;
        if ($CheckedValue == 2) {
            $TrainerRegionSet = $this->common_model->get_selected_values('cmsusers_tregion_rights', 'id,region_id', "userid=" . $Editid);
            if (count((array) $TrainerRegionSet) > 0) {
                foreach ($TrainerRegionSet as $key => $Regionset) {
                    $SelectedTrainerSet = $this->common_model->get_selected_values('cmsusers_rights', 'rightsuser_id', ' userid=' . $Editid . ' AND rights_id=' . $Regionset->id);
                    if ($key == 0) {
                        $dtWhere .= " AND (u.region_id=" . $Regionset->region_id;
                    } else {
                        $dtWhere .= " OR (u.region_id=" . $Regionset->region_id;
                    }
                    if (count((array) $SelectedTrainerSet) > 0) {
                        $List = "";
                        foreach ($SelectedTrainerSet as $value) {
                            $List .= $value->rightsuser_id . ",";
                        }
                        $dtWhere .= " AND u.userid IN(" . rtrim($List, ",") . ")";
                    }
                    $dtWhere .= ")";
                }
            } else {
                $dtWhere .= " AND u.userid IN ($Editid)";
            }
        }
        $DTRenderArray = $this->company_users_model->getUserrightsData($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('userid', 'region_name', 'name', 'email', 'designation', 'Actions');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<button type="button" id="remove" value="' . $dtRow['userid'] . '" name="remove"  class="btn btn-danger btn-sm delete"'
                        . '  ' . ($CheckedValue == 1 ? 'disabled' : '') . '><i class="fa fa-times"></i></button>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function WorkshopRights_table($id)
    {
        $Editid = base64_decode($id);
        $dtSearchColumns = array('u.id', 'r.region_name', 'wt.workshop_type', 'u.workshop_name', 'u.start_date', 'u.end_date');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $CurrnetUserData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $Editid);
        if ($CurrnetUserData->company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1";
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1";
            }
        }
        $CheckedValue = ($this->input->get('CheckedValue') ? $this->input->get('CheckedValue') : '');
        $DedaultRights = false;
        if ($CheckedValue == 2) {
            $WorkshopRegionSet = $this->common_model->get_selected_values('cmsusers_wregion_rights', 'id,region_id', "userid=" . $Editid);
            if (count((array) $WorkshopRegionSet) > 0) {
                foreach ($WorkshopRegionSet as $key => $regionSet) {

                    $SelectedWorkshopSet = $this->common_model->get_selected_values('cmsusers_wtype_rights', 'workshop_type_id', ' userid=' . $Editid . ' AND rights_id=' . $regionSet->id);
                    if ($key == 0) {
                        $dtWhere .= " AND (u.region=" . $regionSet->region_id;
                    } else {
                        $dtWhere .= " OR (u.region=" . $regionSet->region_id;
                    }
                    if (count((array) $SelectedWorkshopSet) > 0) {
                        $List = "";
                        foreach ($SelectedWorkshopSet as $value) {
                            $List .= $value->workshop_type_id . ",";
                        }
                        $dtWhere .= " AND u.workshop_type IN(" . rtrim($List, ",") . ")";
                    }
                    $SelectedWorkshopSet = $this->common_model->get_selected_values('cmsusers_workshop_rights', 'workshop_id', ' userid=' . $Editid . ' AND rights_id=' . $regionSet->id);
                    if (count((array) $SelectedWorkshopSet) > 0) {
                        $List = "";
                        foreach ($SelectedWorkshopSet as $value) {
                            $List .= $value->workshop_id . ",";
                        }
                        $dtWhere .= " AND u.id IN(" . rtrim($List, ",") . ")";
                    }
                    $dtWhere .= ")";
                }
            } else {
                $DedaultRights = true;
            }
        }
        if (!$DedaultRights) {
            $DTRenderArray = $this->company_users_model->getUserWorkshopData($dtWhere, $dtOrder, $dtLimit);
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
        }


        $dtDisplayColumns = array('id', 'region_name', 'workshop_type', 'workshop_name', 'start_date', 'end_date');
        if (isset($DTRenderArray['ResultSet']) && count((array) $DTRenderArray['ResultSet']) > 0) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array) $dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == "Actions") {
                        $action = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete"'
                            . '  ' . ($CheckedValue == 1 || $CheckedValue == 2 ? 'disabled' : '') . '><i class="fa fa-times"></i></button>';
                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }
        echo json_encode($output);
    }

    public function WorkshopRegion_table($id)
    {
        $Editid = base64_decode($id);
        $dtSearchColumns = array('id', 'region_name');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $CurrnetUserData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $Editid);
        if ($CurrnetUserData->company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1";
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $CurrnetUserData->company_id . " AND u.status=1";
            }
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.id IN (SELECT region_id FROM cmsusers_wregion_rights where userid  = " . $Editid . ")";
        } else {
            $dtWhere .= " WHERE u.id IN (SELECT region_id FROM cmsusers_wregion_rights where userid  = " . $Editid . ")";
        }
        $CheckedValue = ($this->input->get('CheckedValue') ? $this->input->get('CheckedValue') : '');
        $DTRenderArray = $this->company_users_model->getUserRegionWorkshopData($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id', 'region_name', 'totalWorkshop', 'Actions');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete"'
                        . '  ' . ($CheckedValue == 1 ? 'disabled' : '') . '><i class="fa fa-times"></i></button>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function DatatableRefresh()
    {
        //'ar.rolename',
        $dtSearchColumns = array('u.userid', 'u.userid', 'co.company_name', 'rg.region_name', 'CONCAT(u.first_name, " ",u.last_name)', 'u.username', 'u.email', 'u.addeddate', 'ar.rolename', 'd.description', 'u.status', 'userid');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('filter_cmp') ? $this->input->get('filter_cmp') : '');
            if ($cmp_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.company_id  = " . $cmp_id;
                } else {
                    $dtWhere .= " WHERE u.company_id  = " . $cmp_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $filter_region_id = ($this->input->get('filter_region_id') ? $this->input->get('filter_region_id') : '');
        if ($filter_region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.region_id  = " . $filter_region_id;
            } else {
                $dtWhere .= " WHERE u.region_id  = " . $filter_region_id;
            }
        }
        $filter_design_id = ($this->input->get('filter_design_id') ? $this->input->get('filter_design_id') : '');
        if ($filter_design_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.designation_id  = " . $filter_design_id;
            } else {
                $dtWhere .= " WHERE u.designation_id  = " . $filter_design_id;
            }
        }
        $filter_role_id = ($this->input->get('filter_role_id') ? $this->input->get('filter_role_id') : '');
        if ($filter_role_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.role  = " . $filter_role_id;
            } else {
                $dtWhere .= " WHERE u.role  = " . $filter_role_id;
            }
        }
        $status = $this->input->get('filter_status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status  = " . $status;
            } else {
                $dtWhere .= " WHERE u.status  = " . $status;
            }
        }

        $DTRenderArray = $this->company_users_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        ); //rolename
        $dtDisplayColumns = array('checkbox', 'userid', 'company_name', 'region_name', 'name', 'username', 'email', 'addeddate', 'rolename', 'designation', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            //$User_Name = $dtRow['salutation'] . ' ' . $dtRow['first_name'] . ' ' . $dtRow['last_name'];
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add || $acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'company_users/view/' . base64_encode($dtRow['userid']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'company_users/edit/' . base64_encode($dtRow['userid']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_add) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'company_users/copy/' . base64_encode($dtRow['userid']) . '">
                                        <i class="fa fa-copy"></i>&nbsp;Copy
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['userid']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        $action .= '</ul>
                            </div>';
                    } else {
                        $action = '<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                Locked&nbsp;&nbsp;<i class="fa fa-lock"></i>
                            </button>';
                    }

                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function submit($Copy_id = "")
    {
        if ($Copy_id != "") {
            $Copy_id = base64_decode($Copy_id);
        }
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {

            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('login_type', 'Login Type', 'required');
            $this->form_validation->set_rules('region_id', 'Region', 'required');
            $this->form_validation->set_rules('loginid', 'Username', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('confirmpassword', 'Confirm password', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            $this->form_validation->set_rules('roleid', 'Role', 'trim|required');
            $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[50]');
            $this->form_validation->set_rules('depart', 'Department', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $UserName = $this->input->post('loginid');
                if (preg_match('/\s/', $UserName) > 0) {
                    $Message = "Space are not allowed";
                    $SuccessFlag = 0;
                } else {
                    $DuplicateFlag = $this->company_users_model->check_Login_id($UserName, '', $Company_id);
                    if ($DuplicateFlag) {
                        $Message = "Login ID already exists!!!";
                        $SuccessFlag = 0;
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'login_type' => $this->input->post('login_type'),
                        'region_id' => $this->input->post('region_id'),
                        'username' => $this->input->post('loginid'),
                        'password' => $this->common_model->encrypt_password($this->input->post('password')),
                        'role' => $this->input->post('roleid'),
                        'designation_id' => $this->input->post('designation'),
                        'department' => $this->input->post('depart'),
                        'salutation' => $this->input->post('salutation'),
                        'first_name' => ucfirst(strtolower($this->input->post('first_name'))),
                        'last_name' => ucfirst(strtolower($this->input->post('last_name'))),
                        'address1' => $this->input->post('address'),
                        'address2' => $this->input->post('address2'),
                        'city' => $this->input->post('city_id'),
                        'state' => $this->input->post('state_id'),
                        'country' => $this->input->post('country_id'),
                        'pincode' => $this->input->post('pincode'),
                        'email' => $this->input->post('email'),
                        'email2' => $this->input->post('email2'),
                        'mobile' => $this->input->post('mobile'),
                        'contactno' => $this->input->post('contactno'),
                        'fax' => $this->input->post('fax'),
                        'note' => $this->input->post('description'),
                        //'avatar' => $this->input->post('avatar_path'),
                        'emp_id' => strtoupper($this->input->post('emp_id')),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0,
                        'userrights_type' => 1,
                        'workshoprights_type' => 1
                    );
                    $Insert_ID = $this->common_model->insert('company_users', $data);
                    //echo $this->db->last_query();
                    if ($Insert_ID != '') {
                        $Message = "CMS User created successfully.";
                        $Rdata['id'] = base64_encode($Insert_ID);
                    } else {
                        $Message = "Error while Adding User,Contact Mediaworks for technical support.!";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encoded_id)
    {
        $SuccessFlag = 1;
        $Message = '';
        $id = base64_decode($Encoded_id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {

            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                //$this->form_validation->set_rules('company_id', 'Company name', 'required');
                //$Company_id = $this->input->post('company_id');
                $oldData = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $id);
                $Company_id = $oldData->company_id;
            } else {
                $Company_id = $this->mw_session['company_id'];
            }


            $this->form_validation->set_rules('login_type', 'Login Type', 'required');
            $this->form_validation->set_rules('roleid', 'Role', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('loginid', 'Username', 'trim|required|max_length[50]');
            //$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
            //$this->form_validation->set_rules('confirmpassword', 'Confirm password', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('region_id', 'Region', 'required');
            $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[50]');
            $this->form_validation->set_rules('depart', 'Department', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $UserName = $this->input->post('loginid');
                if (preg_match('/\s/', $UserName) > 0) {
                    $Message = "Space are not allowed";
                    $SuccessFlag = 0;
                } else {
                    $DuplicateFlag = $this->company_users_model->check_Login_id($UserName, $id, $Company_id);
                    if ($DuplicateFlag) {
                        $Message = "Login ID already exists!!!";
                        $SuccessFlag = 0;
                    }
                }

                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        // 'company_id' => $Company_id,
                        'emp_id' => strtoupper($this->input->post('emp_id')),
                        'login_type' => $this->input->post('login_type'),
                        'username' => $this->input->post('loginid'),
                        'role' => $this->input->post('roleid'),
                        'region_id' => $this->input->post('region_id'),
                        'designation_id' => $this->input->post('designation'),
                        'department' => $this->input->post('depart'),
                        'salutation' => $this->input->post('salutation'),
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'address1' => $this->input->post('address'),
                        'address2' => $this->input->post('address2'),
                        'city' => $this->input->post('city_id'),
                        'state' => $this->input->post('state_id'),
                        'country' => $this->input->post('country_id'),
                        'pincode' => $this->input->post('pincode'),
                        'email' => $this->input->post('email'),
                        'email2' => $this->input->post('email2'),
                        'mobile' => $this->input->post('mobile'),
                        'contactno' => $this->input->post('contactno'),
                        'fax' => $this->input->post('fax'),
                        'note' => $this->input->post('description'),
                        'avatar' => $this->input->post('avatar_path'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    //                    if ($LoggedUserData->login_type == 4) {
//                        $data['login_type'] = $this->input->post('login_type');
//                        $data['role'] = $this->input->post('roleid');
//                    }
                    if ($this->input->post('tpassword') != '') {
                        $data['password'] = $this->common_model->encrypt_password($this->input->post('tpassword'));
                    }
                    $this->common_model->update('company_users', 'userid', $id, $data);
                    $Message = "User data updated successfully";
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove()
    {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = base64_decode($this->input->Post('deleteid'));

            $this->company_users_model->remove($deleted_id);
            $this->common_model->delete('cmsusers_rights', 'userid', $deleted_id);
            $this->common_model->delete('temp_trights', 'user_id', $deleted_id);
            $this->common_model->delete('cmsusers_workshop_rights', 'userid', $deleted_id);
            $this->common_model->delete('cmsusers_wregion_rights', 'userid', $deleted_id);
            $this->common_model->delete_whereclause("temp_wrights", " user_id=" . $deleted_id);
            $message = "User deleted successfully.";
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action)
    {
        $action_id = $this->input->Post('id');
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        $title = '';
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']
                );
                $this->common_model->update('company_users', 'userid', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                // $StatusFlag = $this->company_users_model->CheckUserAssignRole($id);
                $StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('company_users', 'userid', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. User(s) assigned to role!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                // $DeleteFlag = $this->company_users_model->CheckUserAssignRole($id);
                $DeleteFlag = true;
                if ($DeleteFlag) {
                    $this->common_model->delete('company_users', 'userid', $id);
                    $this->common_model->delete('cmsusers_rights', 'userid', $id);
                    $this->common_model->delete('temp_trights', 'user_id', $id);
                    $this->common_model->delete('cmsusers_workshop_rights', 'userid', $id);
                    $this->common_model->delete('cmsusers_wregion_rights', 'userid', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Role cannot be deleted. User(s) assigned to role!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'User(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function validate()
    {
        $status = $this->company_users_model->validate($this->input->post());
        echo $status;
    }

    public function validate_edit()
    {
        $status = $this->company_users_model->validate_edit($this->input->post());
        echo $status;
    }

    public function Check_emailid()
    {
        $emailid = $this->input->post('email_id', true);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $cmp_user = $this->input->post('cmp_user', true);
        if ($cmp_user != "") {
            $cmp_user = base64_decode($cmp_user);
        }
        echo $this->company_users_model->check_email($emailid, $cmp_user, $company_id);
    }
    public function Check_empcode()
    {
        $emp_id = $this->input->post('emp_id', true);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $cmp_user = $this->input->post('cmp_user', true);
        if ($cmp_user != "") {
            $cmp_user = base64_decode($cmp_user);
        }
        $exist_array = $this->company_users_model->DuplicateEmployeeCode($emp_id, $company_id, $cmp_user);
        echo (count((array) $exist_array) > 0 ? true : false);
    }

    public function Check_firstlast()
    {
        $cmp_user = $this->input->post('cmp_user', true);
        $cmp_id = $this->input->post('cmp_id', true);
        $fname = $this->input->post('firstname', true);
        $lname = $this->input->post('lastname', true);
        echo $this->company_users_model->check_firstlast($fname, $lname, $cmp_id, $cmp_user);
    }

    public function Check_loginID()
    {
        $login_id = $this->input->post('login_id', true);
        $user_id = $this->input->post('userid', true);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($user_id != "") {
            $user_id = base64_decode($user_id);
        }
        echo $this->company_users_model->check_Login_id($login_id, $user_id, $company_id);
    }

    public function temp_avatar_upload($id)
    {
        $this->load->library('upload');
        $now = date('Y-m-d H:i:s');
        $Editid = base64_decode($id);
        $ImageFormat = explode('.', $_FILES['avatar_file']['name']);
        $Success = 1;
        $image = '';
        $Msg = "";
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Msg = "You have no rights to edit,Contact Administrator for rights";
            $Success = 0;
        } else {
            $OldData = $this->common_model->get_value('company_users', 'avatar,company_id', 'userid=' . $Editid);
            $image_Name = time();
            $upload_path = 'assets/uploads/avatar/' . $image_Name;
            //            if ($this->mw_session['company_id'] != "") {
//                $upload_path = '../mwadmin/assets/uploads/avatar/' . $image_Name;
//            } else {
//                $upload_path = 'assets/uploads/avatar/' . $image_Name;
//            }
            if ($Success) {
                $crop = new CropAvatar(
                    $this->input->post('avatar_src'), $this->input->post('avatar_data'), $_FILES['avatar_file'],
                    $upload_path
                );
                if ($crop->getMsg() != "") {
                    $Msg = $crop->getMsg();
                } else {
                    if ($OldData->avatar != "") {
                        $Path = "./assets/uploads/avatar/" . $OldData->avatar;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                    $UData = array(
                        'avatar' => $image_Name . '.png',
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('company_users', 'userid', $Editid, $UData);
                }


                //$config['upload_path'] = $upload_path;
                //$config['overwrite'] = FALSE;
                //$config['allowed_types'] = 'gif|jpg|png|jpeg';
                //                $config['max_width'] = 750;
                //                $config['max_height'] = 400;
                //                $config['min_width'] = 750;
                //                $config['min_height'] = 400;
                //$config['file_name'] = $NewImageName;
                //$this->load->library('upload', $config);
                //$this->upload->initialize($config);
//                if (!$this->upload->do_upload('avatar_file')) {
//                    $message = $this->upload->display_errors();
//                    $Success=0;
//                } else {
//
//                    
//                }


                $response = array(
                    'state' => 200,
                    'message' => $crop->getMsg(),
                    'result' => $crop->getResult(),
                    'preview' => $crop->getTemporery()
                );
            }
        }
        if (!$Success) {
            $response = array(
                'state' => 100,
                'message' => $Msg,
                'result' => '',
                'preview' => ''
            );
        }
        echo json_encode($response);
    }

    public function getTrainerList($Encode_id)
    {
        $user_id = base64_decode($Encode_id);
        $RegionArray = $this->input->post('Trainer_region', true);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $WhereCond = " userid !=" . $user_id . " AND status=1 AND company_id=" . $company_id;
        if ($RegionArray != null) {
            $WhereCond .= " AND region_id IN(" . $RegionArray . ")";
        }
        $TrainerList = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name," ",last_name) as name', $WhereCond);
        $cust_trainer = $this->input->post('cust_trainer', true);
        if ($cust_trainer == null) {
            $cust_trainer = array();
        }
        $lcHtml = "";
        if (count((array) $TrainerList) > 0) {
            foreach ($TrainerList as $value) {
                $lcHtml .= '<option value="' . $value->userid . '" ' . (in_array($value->userid, $cust_trainer) ? 'Selected' : '') . '  >' . $value->name . '</option>';
            }
        }
        echo $lcHtml;
    }

    public function getWorkshopList($Encode_id)
    {
        $user_id = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $WhereCond = " status=1 AND company_id=" . $company_id;
        $RegionArray = $this->input->post('workshop_region', true);
        if ($RegionArray != null) {
            $WhereCond .= " AND region IN(" . $RegionArray . ")";
        }
        $lcHtml = "";
        $WorkshopTypeArray = $this->input->post('workshop_type', true);
        if ($WorkshopTypeArray != null && count((array) $WorkshopTypeArray) > 0) {
            $TypeList = implode(',', $WorkshopTypeArray);
            $WhereCond .= " AND workshop_type IN(" . $TypeList . ")";
            $WorkshopList = $this->common_model->get_selected_values('workshop', 'id,workshop_name', $WhereCond);
            $custom_workshop = $this->input->post('custom_workshop', true);
            if ($custom_workshop == null) {
                $custom_workshop = array();
            }

            if (count((array) $WorkshopList) > 0) {
                foreach ($WorkshopList as $value) {
                    $lcHtml .= '<option value="' . $value->id . '" ' . (in_array($value->id, $custom_workshop) ? 'Selected' : '') . '>' . $value->workshop_name . '</option>';
                }
            }
        }
        echo $lcHtml;
    }

    public function getTrainerRow($Encode_id)
    {
        $user_id = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $TRightsRow = $this->input->post('TRightsRow', true);
        $RegionRowset = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $company_id);
        $htdata = '<tr id="Row_' . $TRightsRow . '">';
        $htdata .= '<td><select id="Tregion_id' . $TRightsRow . '" name="Tregion_id[]" class="form-control input-sm select2"  style="width:100%" onchange="LoadCustomTrainer(' . $TRightsRow . ');">';
        $htdata .= '<option value="">Please Select</option>';
        foreach ($RegionRowset as $tr) {
            $htdata .= '<option value="' . $tr->id . '">' . $tr->region_name . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<input type="hidden" value="' . $TRightsRow . '" name="TotalRow[]">';
        $htdata .= '<td><select id="TRTrainer_id' . $TRightsRow . '" name="TRTrainer_id' . $TRightsRow . '[]" class="form-control input-sm select2" style="width:100%" multiple="" >';
        $htdata .= '</select></td>';
        $htdata .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $TRightsRow . ')";><i class="fa fa-times"></i></button> </td></tr>';
        $data['htmlData'] = $htdata;
        echo json_encode($data);
    }

    public function getWorkshopRow($Encode_id)
    {
        $user_id = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $WRightsRow = $this->input->post('WRightsRow', true);
        $RegionRowset = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $company_id);
        $WorkshopTypeset = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 AND company_id=' . $company_id);
        //$Workshopset =$this->common_model->get_selected_values('workshop', 'id,workshop_name', 'status=1 AND company_id=' . $company_id);
        //$tpdata = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $cmp_id);
        $htdata = '<tr id="Row_' . $WRightsRow . '">';
        $htdata .= '<td><select id="WRegion_id' . $WRightsRow . '" name="Wregion_id[]" class="form-control input-sm select2"  style="width:100%" onchange="LoadCustomWorkshop(' . $WRightsRow . ');">';
        $htdata .= '<option value="">Please Select</option>';
        foreach ($RegionRowset as $tr) {
            $htdata .= '<option value="' . $tr->id . '">' . $tr->region_name . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<input type="hidden" value="' . $WRightsRow . '" name="TotalRow[]">';
        $htdata .= '<td><select id="Workshop_type_id' . $WRightsRow . '" name="Workshop_type_id' . $WRightsRow . '[]" class="form-control input-sm select2 ValueUnq" multiple="" style="width:100%" onchange="LoadCustomWorkshop(' . $WRightsRow . ');">';
        $htdata .= '';
        foreach ($WorkshopTypeset as $tp) {
            $htdata .= '<option value="' . $tp->id . '">' . $tp->workshop_type . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<td><select id="Workshop_id' . $WRightsRow . '" name="Workshop_id' . $WRightsRow . '[]" class="form-control input-sm select2" style="width:100%" multiple="" >';
        //        foreach ($Workshopset as $tp) {
//            $htdata .= '<option value="' . $tp->id . '">' . $tp->workshop_name . '</option>';
//        }
        $htdata .= '</select></td>';
        $htdata .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $WRightsRow . ')";><i class="fa fa-times"></i></button> </td></tr>';
        $data['htmlData'] = $htdata;
        echo json_encode($data);
    }
    public function import()
    {
        $data['module_id'] = '1.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('company_users');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('company_users/import', $data);
    }

    public function userssamplexls()
    {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(
            array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'FF0000'
                )
            )
        );
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()
            ->setCellValue('A2', 'Employee Code*')
            ->setCellValue('B2', 'First Name')
            ->setCellValue('C2', 'Last Name')
            ->setCellValue('D2', 'Email*')
            ->setCellValue('E2', 'Region*')
            ->setCellValue('F2', 'Department*')
            ->setCellValue('G2', 'Designation')
            ->setCellValue('H2', 'Role*')
            ->setCellValue('I2', 'Username*')
            ->setCellValue('J2', 'Password*');


        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:J2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("35");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('G')->setWidth("25");
        $Excel->getActiveSheet()->getColumnDimension('H')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('I')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('J')->setWidth("20");

        $Excel->getActiveSheet()->getStyle('A2:J2')->getFill()->applyFromArray(
            array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'eb3a12'
                )
            )
        );
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Company_User_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        if (ob_get_length())
            ob_end_clean();
        $objWriter->save('php://output');
    }


    public function UploadXls()
    {
        $Message = '';
        $SuccessFlag = 1;

        $this->load->model('company_model');
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "Excel file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 10) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    //                
//                    $First_name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
//                    if ($First_name == '') {
//                        $SuccessFlag = 0;
//                        $Message .= "Row No. $row, First Name is Empty. </br> ";
//                        continue;
//                    }
//                    $Last_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
//                    if ($Last_name == '') {
//                        $SuccessFlag = 0;
//                        $Message .= "Row No. $row, Last Name is Empty. </br> ";
//                        continue;
//                    }
                    $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Emp_code == '') {
                        //print_r($row);
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Employee Code is Empty. </br> ";
                        continue;
                    } else {
                        $Emp_idDuplicateCheck = $this->company_users_model->DuplicateEmployeeCode($Emp_code);
                        if (count((array) $Emp_idDuplicateCheck) > 0) {
                            $Message .= "Row No. $row,Employee Id Already exists.!<br/>";
                            $SuccessFlag = 0;
                            continue;
                        }
                    }
                    $username = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    if ($username == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Username is Empty. </br> ";
                        continue;
                    } else {
                        $EmailDuplicateCheck = $this->common_model->get_value('company_users', 'userid', "username LIKE '" . trim($username) . "'");
                        if (count((array) $EmailDuplicateCheck) > 0) {
                            $Message .= "Row No. $row ,Username  Already exists.!<br/>";
                            $SuccessFlag = 0;
                            continue;
                        }
                    }
                    $dept = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    if ($dept == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Department is Empty. </br> ";
                        continue;
                    }

                    $role = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    if ($role == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Role is Empty. </br> ";
                        continue;
                    }
                    $Pwd = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    if ($Pwd == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Password is Empty. </br> ";
                        continue;
                    }
                    $Email = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    if ($Email == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Email is Empty. </br> ";
                        continue;
                    } else {
                        //                        $EmailDuplicateCheck = $this->common_model->DuplicateEmail($Email, $Company_id);
//                        $EmailDuplicateCheck = $this->common_model->get_value('company_users', 'userid', "email LIKE '" . $Email . "' AND company_id=" . $Company_id);
                        $EmailDuplicateCheck = $this->common_model->get_value('company_users', 'userid', "email LIKE '" . $Email . "'");
                        if (count((array) $EmailDuplicateCheck) > 0) {
                            $Message .= "Row No. $row,Email Id Already exists.!<br/>";
                            $SuccessFlag = 0;
                            continue;
                        }
                    }
                    $Region = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    if ($Region != '') {
                        $regionId = $this->common_model->get_value('region', 'id', " region_name LIKE '" . trim($Region) . "' AND company_id=" . $Company_id);
                        if (count((array) $regionId) == 0) {
                            if (preg_match('/^[a-z0-9 .\-]+$/i', $Region)) {
                                $now = date('Y-m-d H:i:s');
                                $region_data = array(
                                    'company_id' => $Company_id,
                                    'region_name' => $Region,
                                    'status' => '1',
                                    'addeddate' => $now,
                                    'addedby' => $this->mw_session['user_id'],
                                    'deleted' => 0
                                );
                                $regionId = $this->common_model->insert('region', $region_data);
                            } else {
                                $Message .= "Row No. $row,Please enter valid Region with alphabets,numbers and space only!<br/>";
                                // $Message .= "Invalid Region,Please enter valid Region!<br/>";
                                $SuccessFlag = 0;
                                continue;
                            }
                        }
                    }
                    // 

                    $designationchk = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    if ($designationchk != '') {
                        $designationId = $this->common_model->get_value('designation', 'id', " description LIKE '" . trim($designationchk) . "' AND company_id=" . $Company_id);
                        if (count((array) $designationId) == 0) {
                            $Message .= "Row No. $row,Invalid Designation,Please enter valid Designation!<br/>";
                            $SuccessFlag = 0;
                            continue;
                        }
                    }
                    $rolechk = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    if ($rolechk != '') {
                        $roleId = $this->common_model->get_value('company_roles', 'arid', "rolename LIKE '" . trim($rolechk) . "' AND company_id=" . $Company_id);
                        if (count((array) $roleId) == 0) {
                            $Message .= "Row No. $row,Invalid Role,Please enter valid Role!<br/>";
                            $SuccessFlag = 0;
                            continue;
                        }
                    }
                }
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $pwd = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $designationId = 0;
                    $regionId = 0;
                    $roleId = 0;
                    $region = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    if ($region != '') {
                        $region_set = $this->common_model->get_value('region', 'id', "region_name LIKE '" . trim($region) . "' AND company_id=" . $Company_id);
                        if (count((array) $region_set) > 0) {
                            $regionId = $region_set->id;
                        }
                    }
                    $designation = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    if ($designation != '') {
                        $designation_set = $this->common_model->get_value('designation', 'id', "description LIKE '" . trim($designation) . "' AND company_id=" . $Company_id);
                        if (count((array) $designation_set) > 0) {
                            $designationId = $designation_set->id;
                        }
                    }
                    $role = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    if ($role != '') {
                        $role_set = $this->common_model->get_value('company_roles', 'arid', "rolename LIKE '" . trim($role) . "' AND company_id=" . $Company_id);
                        if (count((array) $role_set) > 0) {
                            $roleId = $role_set->arid;
                        }
                    } else {
                        $roleId = "2";
                    }

                    $username = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $check_duplicate = $this->common_model->get_value('company_users', 'userid', "username LIKE '" . trim($username) . "'");
                    if (count((array) $check_duplicate) > 0) {
                        $Message .= "Row No. $row,Username  Already exists.!<br/>";
                        $SuccessFlag = 0;
                        continue;
                    }

                    $Counter++;
                    $data = array(
                        'company_id' => $Company_id,
                        'emp_id' => strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue()),
                        'first_name' => ucwords($worksheet->getCellByColumnAndRow(2, $row)->getValue()),
                        'last_name' => ucwords($worksheet->getCellByColumnAndRow(3, $row)->getValue()),
                        'email' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                        'region_id' => $regionId,
                        'department' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                        'designation_id' => $designationId,
                        'role' => $roleId,
                        'username' => $username,
                        'password' => $this->common_model->encrypt_password($pwd),
                        'status' => 1,
                        'deleted' => 0,
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->insert('company_users', $data);
                    $Message = $Counter . " Company Users Imported successfully.";
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function file_check($str)
    {
        $allowed_mime_type_arr = array('application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .xlsx or.xls file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select xls to import.');
            return false;
        }
    }
    public function get_encrypt_password($password)
    {
        echo $this->common_model->encrypt_password($password);
        /*$CurrentUserData = $this->common_model->get_selected_values('company_users', '*', 'status=1');
              foreach ($CurrentUserData as $tp) {
                  $UData = array('password' => $this->common_model->encrypt_password('eNable@2021'),
                  );
                  $this->common_model->update('company_users', 'userid', $tp->userid, $UData);
              }*/

    }
    public function export_user()
    {
        $DTRenderArray = $this->company_users_model->ExportUserData();
        $x = 0;
        $user_list = [];
        foreach ($DTRenderArray as $ud) {
            $user_list[$x]['User Id'] = $ud->userid;
            $user_list[$x]['Employee Code'] = $ud->emp_id;
            $user_list[$x]['Region'] = $ud->region_name;
            $user_list[$x]['Name'] = $ud->name;
            $user_list[$x]['Email'] = $ud->email;
            $user_list[$x]['Registration Date'] = $ud->addeddate;
            $user_list[$x]['Role'] = $ud->rolename;
            $user_list[$x]['Department'] = $ud->department;
            $user_list[$x]['Designation'] = $ud->designation;
            $user_list[$x]['Status'] = ($ud->status ? 'Active' : 'In-Active');
            $x++;
        }
        $Data_list = $user_list;
        $this->load->library('PHPExcel');
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);
        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );
        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
            )
        );
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray_body);
        $i = 1;
        $j = 1;
        $dtDisplayColumns1 = array_keys($Data_list[0]);
        foreach ($dtDisplayColumns1 as $column) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $column);
            $j++;
        }
        $j = 2;
        foreach ($Data_list as $value) {
            $i = 1;
            foreach ($dtDisplayColumns1 as $column) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                $i++;
            }
            $j++;
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . "CMS_users.xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        $objWriter->save('php://output');
        redirect('company_users');
    }
}