<?php
/*
Plugin Name: Rotary Membership Form
Plugin URI: https://rotaryclubyangonsouth.org
Description: Complete Rotary Membership Application Form matching the official template
Version: 2.5
Author: Rotary Club Yangon South
Text Domain: rotary-membership
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Database table name
global $wpdb;
define('ROTARY_TABLE', $wpdb->prefix . 'rotary_membership');

// Activation hook - create table
register_activation_hook(__FILE__, 'rotary_install_table');
function rotary_install_table() {
    global $wpdb;
    
    $table_name = ROTARY_TABLE;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Complete table structure matching the form
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        nickname VARCHAR(255),
        home_address TEXT NOT NULL,
        email VARCHAR(255) NOT NULL,
        business_email VARCHAR(255),
        mobile_no VARCHAR(50) NOT NULL,
        business_tel VARCHAR(50),
        business_employer VARCHAR(255),
        current_position VARCHAR(255),
        industry_type VARCHAR(255),
        business_address TEXT,
        date_of_birth DATE,
        preferred_address VARCHAR(50) DEFAULT 'Home',
        personal_assistant VARCHAR(255),
        pa_email VARCHAR(255),
        previous_rotarian TEXT,
        personal_background TEXT NOT NULL,
        applicant_signature VARCHAR(255) NOT NULL,
        application_date DATE,
        nominator_name VARCHAR(255),
        nominator_signature VARCHAR(255) NOT NULL,
        board_approval_date DATE,
        privacy_agreed TINYINT(1) DEFAULT 0,
        photo_url VARCHAR(500),
        photo_attachment_id INT(11),
        status VARCHAR(20) DEFAULT 'pending',
        submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Check for errors
    if ($wpdb->last_error) {
        error_log('Rotary Table Creation Error: ' . $wpdb->last_error);
    }
}

// Initialize plugin
add_action('init', 'rotary_init');
function rotary_init() {
    // Start session for messages
    if (!session_id()) {
        session_start();
    }
    
    // Handle form submission
    if (isset($_POST['rotary_submit']) && !empty($_POST['rotary_nonce'])) {
        if (wp_verify_nonce($_POST['rotary_nonce'], 'rotary_form_nonce')) {
            $result = rotary_process_form();
            
            // Store message in session
            if (!session_id()) session_start();
            
            if ($result === true) {
                $_SESSION['rotary_message'] = array(
                    'type' => 'success',
                    'text' => 'Your Rotary membership application has been submitted successfully!'
                );
                // Clear form data on success
                if (isset($_SESSION['rotary_form_data'])) {
                    unset($_SESSION['rotary_form_data']);
                }
            } else {
                $_SESSION['rotary_message'] = array(
                    'type' => 'error',
                    'text' => $result
                );
                // Don't store file data in session
                $form_data = $_POST;
                unset($form_data['photo_upload']);
                $_SESSION['rotary_form_data'] = $form_data;
            }
            
            // Redirect to prevent resubmission
            wp_redirect(add_query_arg('submitted', '1', $_SERVER['REQUEST_URI']));
            exit;
        }
    }
}

// Shortcode for the form
add_shortcode('rotary_membership_form', 'rotary_display_form');
function rotary_display_form() {
    global $wpdb;
    
    // Check if table exists, create if not
    $table_name = ROTARY_TABLE;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        rotary_install_table();
    }
    
    // Get message from session
    $message = '';
    if (isset($_SESSION['rotary_message'])) {
        $msg = $_SESSION['rotary_message'];
        $message = '<div class="rotary-message ' . esc_attr($msg['type']) . '">' . esc_html($msg['text']) . '</div>';
        unset($_SESSION['rotary_message']);
    }
    
    // Get form data from session if exists
    $form_data = isset($_SESSION['rotary_form_data']) ? $_SESSION['rotary_form_data'] : array();
    
    ob_start();
    ?>
    
    <style>
        .rotary-form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .rotary-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
            font-weight: bold;
        }
        
        .rotary-message.success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .rotary-message.error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .rotary-form {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .rotary-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0073aa;
        }
        
        .rotary-header h2 {
            color: #0073aa;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .rotary-header p {
            color: #666;
            font-size: 16px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            background: #f8f9fa;
            padding: 10px 15px;
            border-left: 4px solid #0073aa;
            border-radius: 4px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }
        
        .form-group label .required {
            color: #e74c3c;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #0073aa;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,115,170,0.1);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 35px;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #0073aa 0%, #005a87 100%);
            color: white;
            padding: 16px 40px;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #005a87 0%, #004466 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,115,170,0.2);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .form-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #6c757d;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
        
        .signature-section {
            background: #f0f7ff;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #cce5ff;
        }
        
        .form-group-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-group-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 20px 0;
            padding: 15px;
            background: #fff3cd;
            border-radius: 4px;
            border-left: 3px solid #ffc107;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-top: 3px;
        }
        
        .checkbox-group label {
            font-weight: normal;
            font-size: 14px;
            line-height: 1.5;
            color: #856404;
        }
        
        .steps-section {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
            border: 1px solid #b8daff;
        }
        
        .steps-section h3 {
            color: #0056b3;
            margin-bottom: 15px;
        }
        
        .steps-section ol {
            margin-left: 20px;
            color: #333;
        }
        
        .steps-section li {
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        /* Photo Upload Styles */
        .photo-upload-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            margin-top: 30px;
        }
        
        .photo-upload-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .photo-preview-container {
            text-align: center;
            margin-bottom: 10px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #0073aa;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
        }
        
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            display: none;
        }
        
        .photo-preview img {
            max-width: 100%;
            max-height: 180px;
            display: block;
            margin: 0 auto;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .photo-upload-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .photo-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .photo-upload-btn {
            background: #28a745;
            color: white;
        }
        
        .photo-upload-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .photo-remove-btn {
            background: #dc3545;
            color: white;
        }
        
        .photo-remove-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .photo-info {
            font-size: 13px;
            color: #666;
            text-align: center;
            margin-top: 10px;
        }
        
        .photo-requirements {
            background: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #ffc107;
            margin-top: 10px;
            font-size: 13px;
        }
        
        .photo-requirements ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .photo-requirements li {
            margin-bottom: 5px;
        }
        
        .photo-placeholder {
            text-align: center;
            color: #6c757d;
        }
        
        .photo-placeholder-icon {
            font-size: 48px;
            color: #adb5bd;
            margin-bottom: 10px;
        }
        
        .photo-placeholder-text {
            font-size: 14px;
            color: #6c757d;
        }
        
        .photo-upload-status {
            display: none;
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
        
        .photo-error {
            display: none;
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
    </style>
    
    <div class="rotary-form-container">
        <?php echo $message; ?>
        
        <div class="rotary-form">
            <div class="rotary-header">
                <h2>Rotary Club of Yangon South</h2>
                <p>Membership Application Form</p>
            </div>
            
            <form method="post" action="" id="rotary-application-form" enctype="multipart/form-data">
                <?php wp_nonce_field('rotary_form_nonce', 'rotary_nonce'); ?>
                <input type="hidden" name="rotary_submit" value="1">
                
                <!-- Section 1: Personal Information -->
                <div class="form-section">
                    <h3>Personal Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required 
                                   value="<?php echo isset($form_data['name']) ? esc_attr($form_data['name']) : ''; ?>"
                                   placeholder="Full Name">
                        </div>
                        
                        <div class="form-group">
                            <label for="nickname">Nick Name</label>
                            <input type="text" id="nickname" name="nickname" class="form-control"
                                   value="<?php echo isset($form_data['nickname']) ? esc_attr($form_data['nickname']) : ''; ?>"
                                   placeholder="Preferred Nickname">
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="home_address">Home Address <span class="required">*</span></label>
                        <textarea id="home_address" name="home_address" class="form-control" required
                                  placeholder="Complete home address"><?php echo isset($form_data['home_address']) ? esc_textarea($form_data['home_address']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Section 2: Contact Information -->
                <div class="form-section">
                    <h3>Contact Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required
                                   value="<?php echo isset($form_data['email']) ? esc_attr($form_data['email']) : ''; ?>"
                                   placeholder="personal@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="business_email">Business Email</label>
                            <input type="email" id="business_email" name="business_email" class="form-control"
                                   value="<?php echo isset($form_data['business_email']) ? esc_attr($form_data['business_email']) : ''; ?>"
                                   placeholder="business@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile_no">Mobile No <span class="required">*</span></label>
                            <input type="tel" id="mobile_no" name="mobile_no" class="form-control" required
                                   value="<?php echo isset($form_data['mobile_no']) ? esc_attr($form_data['mobile_no']) : ''; ?>"
                                   placeholder="+95 9XXXXXXXXX">
                        </div>
                        
                        <div class="form-group">
                            <label for="business_tel">Business Tel</label>
                            <input type="tel" id="business_tel" name="business_tel" class="form-control"
                                   value="<?php echo isset($form_data['business_tel']) ? esc_attr($form_data['business_tel']) : ''; ?>"
                                   placeholder="+95 1 XXXXXXX">
                        </div>
                    </div>
                </div>
                
                <!-- Section 3: Professional Information -->
                <div class="form-section">
                    <h3>Professional Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="business_employer">Business/Employer</label>
                            <input type="text" id="business_employer" name="business_employer" class="form-control"
                                   value="<?php echo isset($form_data['business_employer']) ? esc_attr($form_data['business_employer']) : ''; ?>"
                                   placeholder="Company or organization name">
                        </div>
                        
                        <div class="form-group">
                            <label for="current_position">Current Position</label>
                            <input type="text" id="current_position" name="current_position" class="form-control"
                                   value="<?php echo isset($form_data['current_position']) ? esc_attr($form_data['current_position']) : ''; ?>"
                                   placeholder="Job title">
                        </div>
                        
                        <div class="form-group">
                            <label for="industry_type">Industry Type</label>
                            <input type="text" id="industry_type" name="industry_type" class="form-control"
                                   value="<?php echo isset($form_data['industry_type']) ? esc_attr($form_data['industry_type']) : ''; ?>"
                                   placeholder="e.g., Technology, Healthcare, Finance">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="business_address">Business Address</label>
                            <textarea id="business_address" name="business_address" class="form-control"
                                      placeholder="Complete business address"><?php echo isset($form_data['business_address']) ? esc_textarea($form_data['business_address']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Section 4: Additional Information -->
                <div class="form-section">
                    <h3>Additional Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control"
                                   value="<?php echo isset($form_data['date_of_birth']) ? esc_attr($form_data['date_of_birth']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred_address">Preferred Address <span class="required">*</span></label>
                            <select id="preferred_address" name="preferred_address" class="form-control" required>
                                <option value="">-- Select --</option>
                                <option value="Home" <?php echo (isset($form_data['preferred_address']) && $form_data['preferred_address'] == 'Home') ? 'selected' : ''; ?>>Home</option>
                                <option value="Business" <?php echo (isset($form_data['preferred_address']) && $form_data['preferred_address'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="personal_assistant">Personal Assistant</label>
                            <input type="text" id="personal_assistant" name="personal_assistant" class="form-control"
                                   value="<?php echo isset($form_data['personal_assistant']) ? esc_attr($form_data['personal_assistant']) : ''; ?>"
                                   placeholder="Assistant's name">
                        </div>
                        
                        <div class="form-group">
                            <label for="pa_email">PA Email</label>
                            <input type="email" id="pa_email" name="pa_email" class="form-control"
                                   value="<?php echo isset($form_data['pa_email']) ? esc_attr($form_data['pa_email']) : ''; ?>"
                                   placeholder="assistant@email.com">
                        </div>
                    </div>
                </div>
                
                <!-- Section 5: Rotary Experience -->
                <div class="form-section">
                    <h3>Previous Rotary Experience</h3>
                    
                    <div class="form-group full-width">
                        <label for="previous_rotarian">Were you a Rotarian previously? (if so, when & where?)</label>
                        <textarea id="previous_rotarian" name="previous_rotarian" class="form-control"
                                  placeholder="Please provide details of any previous Rotary experience, including club names and dates"><?php echo isset($form_data['previous_rotarian']) ? esc_textarea($form_data['previous_rotarian']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Section 6: Background Information -->
                <div class="form-section">
                    <h3>Background Information</h3>
                    
                    <div class="form-group full-width">
                        <label for="personal_background">Please state any vocational and personal background, skills & activities that would enhance your consideration as a Rotarian: <span class="required">*</span></label>
                        <textarea id="personal_background" name="personal_background" class="form-control" required
                                  placeholder="Describe your background, skills, experiences, and why you want to join Rotary"><?php echo isset($form_data['personal_background']) ? esc_textarea($form_data['personal_background']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- NEW SECTION: Passport Size Photo Upload -->
                <div class="form-section photo-upload-section">
                    <h3>Passport Size Photo</h3>
                    
                    <div class="photo-upload-container">
                        <div class="photo-preview-container" id="photo_preview_container">
                            <div class="photo-placeholder" id="photo_placeholder">
                                <div class="photo-placeholder-icon">📷</div>
                                <div class="photo-placeholder-text">No photo selected</div>
                                <div class="photo-placeholder-text" style="font-size: 12px; margin-top: 5px;">Click "Choose Photo" to upload from your device</div>
                            </div>
                            <div class="photo-preview" id="photo_preview">
                                <!-- Photo preview will be displayed here -->
                            </div>
                        </div>
                        
                        <div class="photo-upload-buttons">
                            <div class="file-input-wrapper">
                                <button type="button" id="choose_photo_btn" class="photo-btn photo-upload-btn">
                                    📸 Choose Photo
                                </button>
                                <input type="file" id="photo_upload" name="photo_upload" accept="image/*">
                            </div>
                            <button type="button" id="remove_photo_btn" class="photo-btn photo-remove-btn" style="display: none;">
                                ❌ Remove Photo
                            </button>
                        </div>
                        
                        <div class="photo-upload-status" id="photo_upload_status">
                            Photo selected successfully!
                        </div>
                        
                        <div class="photo-error" id="photo_error">
                            Please select a valid image file (JPG, JPEG, PNG)
                        </div>
                        
                        <div class="photo-info">
                            <p>Please upload a recent passport-sized photo (2x2 inches or 51x51 mm)</p>
                        </div>
                        
                        <div class="photo-requirements">
                            <p><strong>Photo Requirements:</strong></p>
                            <ul>
                                <li>Passport size (2x2 inches / 51x51 mm)</li>
                                <li>Recent photo (taken within last 6 months)</li>
                                <li>Clear front view of full face</li>
                                <li>Plain white or light-colored background</li>
                                <li>Professional appearance (business attire preferred)</li>
                                <li>File format: JPG, JPEG, or PNG</li>
                                <li>Max file size: 2MB</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Section 7: Declaration -->
                <div class="form-section">
                    <div class="form-info">
                        <p><strong>Declaration:</strong> I hereby certify that if accepted to Membership of the Rotary Club Yangon South, that I as a Rotarian, will exemplify the Object of Rotary in all my daily contacts and will abide by the constitutional documents of Rotary International and the club. I agree to pay an admission fee and dues in accordance with the bylaws of the club.</p>
                    </div>
                </div>
                
                <!-- Section 8: Signatures -->
                <div class="form-section signature-section">
                    <h3>Signatures</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="applicant_signature">Signature <span class="required">*</span></label>
                            <input type="text" id="applicant_signature" name="applicant_signature" class="form-control" required
                                   value="<?php echo isset($form_data['applicant_signature']) ? esc_attr($form_data['applicant_signature']) : ''; ?>"
                                   placeholder="Type your full name as signature">
                            <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">Applicant's Signature</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="application_date">Date <span class="required">*</span></label>
                            <input type="date" id="application_date" name="application_date" class="form-control" required
                                   value="<?php echo isset($form_data['application_date']) ? esc_attr($form_data['application_date']) : date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="nominator_name">Nominated by</label>
                            <input type="text" id="nominator_name" name="nominator_name" class="form-control"
                                   value="<?php echo isset($form_data['nominator_name']) ? esc_attr($form_data['nominator_name']) : ''; ?>"
                                   placeholder="Nominator's name">
                        </div>
                        
                        <div class="form-group">
                            <label for="nominator_signature">Signature <span class="required">*</span></label>
                            <input type="text" id="nominator_signature" name="nominator_signature" class="form-control" required
                                   value="<?php echo isset($form_data['nominator_signature']) ? esc_attr($form_data['nominator_signature']) : ''; ?>"
                                   placeholder="Nominator's signature">
                        </div>
                    </div>
                    
                    <div class="form-grid" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="board_approval_date">Board Approved on</label>
                            <input type="date" id="board_approval_date" name="board_approval_date" class="form-control"
                                   value="<?php echo isset($form_data['board_approval_date']) ? esc_attr($form_data['board_approval_date']) : ''; ?>">
                            <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">(For office use only)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Privacy Agreement -->
                <div class="checkbox-group">
                    <input type="checkbox" id="privacy_agree" name="privacy_agree" value="1" required>
                    <label for="privacy_agree">
                        I agree to the collection and processing of my personal data in accordance with the Rotary Club's Privacy Policy. 
                        I understand this information will be used solely for membership processing purposes. 
                        <span class="required">*</span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Submit Membership Application</button>
                
                <!-- Steps to Becoming a Member -->
                <div class="steps-section">
                    <h3>STEPS TO BECOMING A MEMBER</h3>
                    <ol>
                        <li>Attend at least two meetings and get to know the members</li>
                        <li>Have your sponsor submit your application</li>
                        <li>The board will vote on your application and submit it to the membership</li>
                        <li>The members will vote on your application at a meeting in which you are absent</li>
                        <li>An invoice for admission fee and club dues will be sent to you and payment should be made accordingly</li>
                        <li>You will have your induction ceremony at the following meeting</li>
                    </ol>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Photo upload functionality
        $('#choose_photo_btn').on('click', function(e) {
            e.preventDefault();
            $('#photo_upload').click();
        });
        
        $('#photo_upload').on('change', function(e) {
            var file = this.files[0];
            
            if (file) {
                // Validate file type
                var validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    $('#photo_error').text('Please select a valid image file (JPG, JPEG, PNG only)').show().delay(5000).fadeOut();
                    $(this).val('');
                    return;
                }
                
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    $('#photo_error').text('File size too large. Maximum size is 2MB.').show().delay(5000).fadeOut();
                    $(this).val('');
                    return;
                }
                
                // Create a preview
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    // Display preview
                    $('#photo_preview').html('<img src="' + e.target.result + '" alt="Selected Photo" style="max-width: 200px; max-height: 200px;">');
                    $('#photo_preview').show();
                    $('#photo_placeholder').hide();
                    
                    // Show remove button
                    $('#remove_photo_btn').show();
                    
                    // Show success message
                    $('#photo_upload_status').text('Photo selected successfully! Ready to upload.').show().delay(3000).fadeOut();
                    $('#photo_error').hide();
                    
                    console.log('Photo selected:', file.name, file.size);
                };
                
                reader.onerror = function() {
                    $('#photo_error').text('Error reading file. Please try another photo.').show().delay(5000).fadeOut();
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        $('#remove_photo_btn').on('click', function(e) {
            e.preventDefault();
            
            // Clear file input
            $('#photo_upload').val('');
            
            // Clear preview
            $('#photo_preview').html('').hide();
            $('#photo_placeholder').show();
            
            // Hide remove button
            $(this).hide();
            
            // Show message
            $('#photo_upload_status').text('Photo removed').show().delay(2000).fadeOut();
        });
        
        // Form validation
        $('#rotary-application-form').on('submit', function(e) {
            var valid = true;
            var errors = [];
            
            // Check required fields
            $(this).find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    valid = false;
                    var fieldName = $(this).closest('.form-group').find('label').text().replace('*', '').trim();
                    errors.push(fieldName + ' is required');
                    $(this).css('border-color', '#e74c3c');
                } else {
                    $(this).css('border-color', '#ddd');
                }
            });
            
            // Email validation
            var email = $('#email').val();
            if (email && !isValidEmail(email)) {
                valid = false;
                errors.push('Please enter a valid email address');
                $('#email').css('border-color', '#e74c3c');
            }
            
            // Business email validation
            var businessEmail = $('#business_email').val();
            if (businessEmail && !isValidEmail(businessEmail)) {
                valid = false;
                errors.push('Please enter a valid business email address');
                $('#business_email').css('border-color', '#e74c3c');
            }
            
            // PA email validation
            var paEmail = $('#pa_email').val();
            if (paEmail && !isValidEmail(paEmail)) {
                valid = false;
                errors.push('Please enter a valid PA email address');
                $('#pa_email').css('border-color', '#e74c3c');
            }
            
            // Application date validation
            var appDate = $('#application_date').val();
            if (appDate) {
                var selectedDate = new Date(appDate);
                var today = new Date();
                if (selectedDate > today) {
                    valid = false;
                    errors.push('Application date cannot be in the future');
                    $('#application_date').css('border-color', '#e74c3c');
                }
            }
            
            if (!valid) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errors.join('\n'));
            }
        });
        
        function isValidEmail(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Set default date for application date
        if (!$('#application_date').val()) {
            $('#application_date').val('<?php echo date('Y-m-d'); ?>');
        }
        
        // Clear error styling on focus
        $('.form-control').on('focus', function() {
            $(this).css('border-color', '#0073aa');
        });
        
        $('.form-control').on('blur', function() {
            if ($(this).hasClass('error')) {
                $(this).css('border-color', '#e74c3c');
            } else {
                $(this).css('border-color', '#ddd');
            }
        });
    });
    </script>
    
    <?php
    return ob_get_clean();
}

// Process form submission
function rotary_process_form() {
    global $wpdb;
    
    // Check required fields
    $required = array(
        'name', 'email', 'mobile_no', 'home_address', 'personal_background',
        'applicant_signature', 'nominator_signature', 'preferred_address',
        'application_date', 'privacy_agree'
    );
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            return "Please fill in all required fields. Missing: " . str_replace('_', ' ', $field);
        }
    }
    
    // Validate emails
    if (!is_email($_POST['email'])) {
        return "Please enter a valid email address.";
    }
    
    if (!empty($_POST['business_email']) && !is_email($_POST['business_email'])) {
        return "Please enter a valid business email address.";
    }
    
    if (!empty($_POST['pa_email']) && !is_email($_POST['pa_email'])) {
        return "Please enter a valid PA email address.";
    }
    
    // Validate dates
    if (!empty($_POST['date_of_birth'])) {
        $dob = DateTime::createFromFormat('Y-m-d', $_POST['date_of_birth']);
        if (!$dob || $dob->format('Y-m-d') !== $_POST['date_of_birth']) {
            return "Please enter a valid date of birth.";
        }
    }
    
    if (!empty($_POST['application_date'])) {
        $appDate = DateTime::createFromFormat('Y-m-d', $_POST['application_date']);
        if (!$appDate || $appDate->format('Y-m-d') !== $_POST['application_date']) {
            return "Please enter a valid application date.";
        }
        
        // Check if application date is not in the future
        $today = new DateTime();
        $appDate = DateTime::createFromFormat('Y-m-d', $_POST['application_date']);
        if ($appDate > $today) {
            return "Application date cannot be in the future.";
        }
    }
    
    // Handle photo upload
    $photo_url = '';
    $photo_attachment_id = 0;
    
    if (!empty($_FILES['photo_upload']['name'])) {
        // Check for upload errors
        if ($_FILES['photo_upload']['error'] !== UPLOAD_ERR_OK) {
            return "Error uploading photo. Please try again.";
        }
        
        // Validate file type
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        $file_type = $_FILES['photo_upload']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            return "Please upload a valid image file (JPG, JPEG, PNG only).";
        }
        
        // Validate file size (2MB max)
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES['photo_upload']['size'] > $max_size) {
            return "File size too large. Maximum size is 2MB.";
        }
        
        // Upload the file to WordPress media library
        $upload = wp_upload_bits($_FILES['photo_upload']['name'], null, file_get_contents($_FILES['photo_upload']['tmp_name']));
        
        if (!$upload['error']) {
            $photo_url = $upload['url'];
            
            // Create WordPress attachment
            $wp_filetype = wp_check_filetype($_FILES['photo_upload']['name'], null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($_FILES['photo_upload']['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            $attach_id = wp_insert_attachment($attachment, $upload['file']);
            
            if (!is_wp_error($attach_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $photo_attachment_id = $attach_id;
            }
        } else {
            return "Failed to upload photo: " . $upload['error'];
        }
    }
    
    // Prepare data
    $data = array(
        'name' => sanitize_text_field($_POST['name']),
        'nickname' => isset($_POST['nickname']) ? sanitize_text_field($_POST['nickname']) : '',
        'home_address' => sanitize_textarea_field($_POST['home_address']),
        'email' => sanitize_email($_POST['email']),
        'business_email' => !empty($_POST['business_email']) ? sanitize_email($_POST['business_email']) : '',
        'mobile_no' => sanitize_text_field($_POST['mobile_no']),
        'business_tel' => isset($_POST['business_tel']) ? sanitize_text_field($_POST['business_tel']) : '',
        'business_employer' => isset($_POST['business_employer']) ? sanitize_text_field($_POST['business_employer']) : '',
        'current_position' => isset($_POST['current_position']) ? sanitize_text_field($_POST['current_position']) : '',
        'industry_type' => isset($_POST['industry_type']) ? sanitize_text_field($_POST['industry_type']) : '',
        'business_address' => isset($_POST['business_address']) ? sanitize_textarea_field($_POST['business_address']) : '',
        'date_of_birth' => !empty($_POST['date_of_birth']) ? sanitize_text_field($_POST['date_of_birth']) : NULL,
        'preferred_address' => sanitize_text_field($_POST['preferred_address']),
        'personal_assistant' => isset($_POST['personal_assistant']) ? sanitize_text_field($_POST['personal_assistant']) : '',
        'pa_email' => !empty($_POST['pa_email']) ? sanitize_email($_POST['pa_email']) : '',
        'previous_rotarian' => isset($_POST['previous_rotarian']) ? sanitize_textarea_field($_POST['previous_rotarian']) : '',
        'personal_background' => sanitize_textarea_field($_POST['personal_background']),
        'applicant_signature' => sanitize_text_field($_POST['applicant_signature']),
        'application_date' => sanitize_text_field($_POST['application_date']),
        'nominator_name' => isset($_POST['nominator_name']) ? sanitize_text_field($_POST['nominator_name']) : '',
        'nominator_signature' => sanitize_text_field($_POST['nominator_signature']),
        'board_approval_date' => !empty($_POST['board_approval_date']) ? sanitize_text_field($_POST['board_approval_date']) : NULL,
        'privacy_agreed' => 1,
        'photo_url' => $photo_url,
        'photo_attachment_id' => $photo_attachment_id,
        'status' => 'pending',
        'submission_date' => current_time('mysql')
    );
    
    // Insert into database
    $table_name = ROTARY_TABLE;
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        rotary_install_table();
    }
    
    // Try to insert
    $result = $wpdb->insert($table_name, $data);
    
    if ($result === false) {
        // Log the error
        error_log('Rotary Form Insert Error: ' . $wpdb->last_error);
        error_log('Insert Data: ' . print_r($data, true));
        
        // Try alternative method
        $result = $wpdb->query($wpdb->prepare(
            "INSERT INTO $table_name (name, nickname, home_address, email, business_email, mobile_no, business_tel, business_employer, current_position, industry_type, business_address, date_of_birth, preferred_address, personal_assistant, pa_email, previous_rotarian, personal_background, applicant_signature, application_date, nominator_name, nominator_signature, board_approval_date, privacy_agreed, photo_url, photo_attachment_id, status, submission_date) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %d, %s, %s)",
            $data['name'], $data['nickname'], $data['home_address'], $data['email'], 
            $data['business_email'], $data['mobile_no'], $data['business_tel'],
            $data['business_employer'], $data['current_position'], $data['industry_type'],
            $data['business_address'], $data['date_of_birth'], $data['preferred_address'],
            $data['personal_assistant'], $data['pa_email'], $data['previous_rotarian'],
            $data['personal_background'], $data['applicant_signature'], $data['application_date'],
            $data['nominator_name'], $data['nominator_signature'], $data['board_approval_date'],
            $data['privacy_agreed'], $data['photo_url'], $data['photo_attachment_id'], $data['status'], $data['submission_date']
        ));
        
        if ($result === false) {
            return "Unable to save your application. Please try again or contact support.";
        }
    }
    
    // Send email notification
    rotary_send_notification($data, $wpdb->insert_id);
    
    return true;
}

// Send email notification
function rotary_send_notification($data, $id) {
    $to = get_option('admin_email');
    $subject = 'New Rotary Membership Application: ' . $data['name'];
    
    $message = "NEW ROTARY MEMBERSHIP APPLICATION\n";
    $message .= "================================\n\n";
    
    $message .= "Application ID: #{$id}\n";
    $message .= "Submitted: " . date('F j, Y g:i a') . "\n\n";
    
    $message .= "PERSONAL INFORMATION:\n";
    $message .= "Name: {$data['name']}\n";
    $message .= "Nickname: " . ($data['nickname'] ?: 'Not provided') . "\n";
    $message .= "Email: {$data['email']}\n";
    $message .= "Business Email: " . ($data['business_email'] ?: 'Not provided') . "\n";
    $message .= "Mobile: {$data['mobile_no']}\n";
    $message .= "Business Tel: " . ($data['business_tel'] ?: 'Not provided') . "\n";
    $message .= "Date of Birth: " . ($data['date_of_birth'] ?: 'Not provided') . "\n";
    $message .= "Preferred Address: {$data['preferred_address']}\n";
    $message .= "Photo Uploaded: " . (!empty($data['photo_url']) ? 'Yes' : 'No') . "\n\n";
    
    $message .= "PROFESSIONAL INFORMATION:\n";
    $message .= "Business/Employer: " . ($data['business_employer'] ?: 'Not provided') . "\n";
    $message .= "Position: " . ($data['current_position'] ?: 'Not provided') . "\n";
    $message .= "Industry: " . ($data['industry_type'] ?: 'Not provided') . "\n\n";
    
    $message .= "SIGNATURES:\n";
    $message .= "Applicant: {$data['applicant_signature']}\n";
    $message .= "Application Date: {$data['application_date']}\n";
    $message .= "Nominator: " . ($data['nominator_name'] ?: 'Not provided') . "\n";
    $message .= "Nominator Signature: {$data['nominator_signature']}\n\n";
    
    $message .= "VIEW COMPLETE APPLICATION:\n";
    $message .= admin_url("admin.php?page=rotary-applications&view={$id}") . "\n\n";
    
    $message .= "This is an automated notification from the Rotary Membership Form.";
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($to, $subject, $message, $headers);
}

// Admin menu
add_action('admin_menu', 'rotary_admin_menu');
function rotary_admin_menu() {
    // Add main menu for both administrators AND editors
    add_menu_page(
        'Rotary Applications',
        'Rotary Applications',
        'edit_pages',
        'rotary-applications',
        'rotary_admin_page',
        'dashicons-groups',
        30
    );
}

// Admin page
function rotary_admin_page() {
    global $wpdb;
    $table_name = ROTARY_TABLE;
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo '<div class="notice notice-error"><p>Database table not found. Please deactivate and reactivate the plugin.</p></div>';
        echo '<div class="notice notice-info"><p>Table name being checked: ' . $table_name . '</p></div>';
        return;
    }
    
    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && wp_verify_nonce($_GET['nonce'], 'delete_application')) {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to delete applications. Only Administrators can delete applications.', 'rotary-membership'));
        }
        
        $id = intval($_GET['id']);
        $result = $wpdb->delete($table_name, array('id' => $id));
        
        if ($result) {
            echo '<div class="notice notice-success"><p>Application deleted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Failed to delete application.</p></div>';
        }
    }
    
    // Handle status update via POST
    if (isset($_POST['update_status']) && isset($_POST['app_id']) && wp_verify_nonce($_POST['status_nonce'], 'update_status')) {
        if (!current_user_can('edit_pages')) {
            wp_die(__('You do not have sufficient permissions to update application status.', 'rotary-membership'));
        }
        
        $id = intval($_POST['app_id']);
        $status = sanitize_text_field($_POST['status']);
        
        $result = $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $id)
        );
        
        if ($result !== false) {
            echo '<div class="notice notice-success"><p>Status updated successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Failed to update status.</p></div>';
        }
    }
    
    // Check if we're viewing a single application
    if (isset($_GET['view'])) {
        rotary_view_single_application(intval($_GET['view']));
        return;
    }
    
    // List all applications
    rotary_list_applications();
}

// Function to list all applications
function rotary_list_applications() {
    global $wpdb;
    $table_name = ROTARY_TABLE;
    
    // Build WHERE clause for filtering
    $where = array();
    $params = array();
    
    // Status filter
    if (!empty($_GET['status']) && in_array($_GET['status'], array('pending', 'approved', 'rejected'))) {
        $where[] = "status = %s";
        $params[] = $_GET['status'];
    }
    
    // Search filter
    if (!empty($_GET['search'])) {
        $where[] = "(name LIKE %s OR email LIKE %s OR business_employer LIKE %s)";
        $search_term = '%' . $wpdb->esc_like($_GET['search']) . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $where_sql = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM $table_name $where_sql";
    if (!empty($params)) {
        $count_query = $wpdb->prepare($count_query, $params);
    }
    $total_applications = $wpdb->get_var($count_query);
    
    // Setup pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Get applications
    $query = "SELECT * FROM $table_name $where_sql ORDER BY submission_date ASC LIMIT %d OFFSET %d";
    $params[] = $per_page;
    $params[] = $offset;
    
    $applications = $wpdb->get_results($wpdb->prepare($query, $params));
    
    // Get statistics
    $stats = $wpdb->get_results("
        SELECT status, COUNT(*) as count 
        FROM $table_name 
        GROUP BY status
    ");
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Rotary Membership Applications</h1>
        
        <hr class="wp-header-end">
        
        <!-- Filters -->
        <div class="rotary-filters" style="margin: 20px 0; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
            <form method="get">
                <input type="hidden" name="page" value="rotary-applications">
                
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" 
                       placeholder="Name, Email, or Company" style="margin-right: 10px;">
                
                <label for="status">Status:</label>
                <select id="status" name="status" style="margin-right: 10px;">
                    <option value="">All Status</option>
                    <option value="pending" <?php selected(isset($_GET['status']) && $_GET['status'] == 'pending'); ?>>Pending</option>
                    <option value="approved" <?php selected(isset($_GET['status']) && $_GET['status'] == 'approved'); ?>>Approved</option>
                    <option value="rejected" <?php selected(isset($_GET['status']) && $_GET['status'] == 'rejected'); ?>>Rejected</option>
                </select>
                
                <button type="submit" class="button">Filter</button>
                
                <?php if (isset($_GET['search']) || isset($_GET['status'])): ?>
                    <a href="?page=rotary-applications" class="button">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Statistics -->
        <div class="rotary-stats" style="margin: 20px 0; display: flex; gap: 15px;">
            <?php
            $colors = array(
                'pending' => '#f39c12',
                'approved' => '#27ae60',
                'rejected' => '#e74c3c'
            );
            
            // Display each status stat
            foreach ($stats as $stat): ?>
                <div style="flex: 1; background: white; border-left: 4px solid <?php echo $colors[$stat->status]; ?>; padding: 15px;">
                    <div style="font-size: 24px; font-weight: bold;"><?php echo $stat->count; ?></div>
                    <div style="text-transform: uppercase; font-size: 12px; color: #666;"><?php echo ucfirst($stat->status); ?></div>
                </div>
            <?php endforeach; ?>
            
            <!-- Total applications -->
            <div style="flex: 1; background: white; border-left: 4px solid #3498db; padding: 15px;">
                <div style="font-size: 24px; font-weight: bold;"><?php echo $total_applications; ?></div>
                <div style="text-transform: uppercase; font-size: 12px; color: #666;">Total</div>
            </div>
        </div>
        
        <!-- Applications Table -->
        <?php if (empty($applications)): ?>
            <div class="notice notice-info">
                <p>No applications found.</p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Company</th>
                        <th>Photo</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $start_number = (($current_page - 1) * $per_page) + 1;
                    foreach ($applications as $index => $app): 
                        $row_number = $start_number + $index;
                    ?>
                        <tr>
                            <td><?php echo $row_number; ?></td>
                            <td>
                                <strong><?php echo esc_html($app->name); ?></strong>
                                <?php if ($app->nickname): ?>
                                    <br><small>(<?php echo esc_html($app->nickname); ?>)</small>
                                <?php endif; ?>
                                <?php if ($app->current_position): ?>
                                    <br><small><?php echo esc_html($app->current_position); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo esc_html($app->email); ?>
                                <?php if ($app->business_email): ?>
                                    <br><small><?php echo esc_html($app->business_email); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo esc_html($app->mobile_no); ?>
                                <?php if ($app->business_tel): ?>
                                    <br><small><?php echo esc_html($app->business_tel); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($app->business_employer); ?></td>
                            <td>
                                <?php if (!empty($app->photo_url)): ?>
                                    <a href="<?php echo esc_url($app->photo_url); ?>" target="_blank" title="View Photo">
                                        <span class="dashicons dashicons-format-image" style="color: #0073aa; font-size: 24px;"></span>
                                    </a>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: #999; font-size: 24px;" title="No Photo"></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge" style="
                                    background: <?php echo $app->status == 'pending' ? '#f39c12' : ($app->status == 'approved' ? '#27ae60' : '#e74c3c'); ?>;
                                    color: white;
                                    padding: 3px 8px;
                                    border-radius: 3px;
                                    font-size: 12px;
                                    display: inline-block;
                                    margin-bottom: 5px;
                                ">
                                    <?php echo ucfirst($app->status); ?>
                                </span>
                                <br>
                                <select class="rotary-status-select" data-id="<?php echo $app->id; ?>" style="font-size: 12px; margin-top: 5px;">
                                    <option value="pending" <?php selected($app->status, 'pending'); ?>>Pending</option>
                                    <option value="approved" <?php selected($app->status, 'approved'); ?>>Approved</option>
                                    <option value="rejected" <?php selected($app->status, 'rejected'); ?>>Rejected</option>
                                </select>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($app->submission_date)); ?><br>
                                <small><?php echo date('g:i a', strtotime($app->submission_date)); ?></small>
                            </td>
                            <td>
                                <a href="?page=rotary-applications&view=<?php echo $app->id; ?>" class="button button-small">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_applications > $per_page): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $total_pages = ceil($total_applications / $per_page);
                        
                        $pagination_args = array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $total_pages,
                            'current' => $current_page,
                        );
                        
                        echo paginate_links($pagination_args);
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.rotary-status-select').on('change', function() {
            var id = $(this).data('id');
            var status = $(this).val();
            var badge = $(this).siblings('.status-badge');
            
            // Update badge immediately for better UX
            badge.text(status.charAt(0).toUpperCase() + status.slice(1));
            badge.css('background', 
                status == 'pending' ? '#f39c12' : 
                status == 'approved' ? '#27ae60' : '#e74c3c'
            );
            
            // Send AJAX request
            $.post(ajaxurl, {
                action: 'rotary_update_status',
                id: id,
                status: status,
                nonce: '<?php echo wp_create_nonce("rotary_status_update"); ?>'
            }, function(response) {
                if (response.success) {
                    console.log('Status updated successfully');
                } else {
                    console.error('Failed to update status');
                    // Revert the change on error
                    $(this).val($(this).data('previous-value'));
                }
            }.bind(this));
            
            // Store previous value in case of error
            $(this).data('previous-value', $(this).val());
        });
    });
    </script>
    <?php
}

// Function to view single application
function rotary_view_single_application($id) {
    global $wpdb;
    $table_name = ROTARY_TABLE;
    
    $app = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    
    if (!$app) {
        echo '<div class="notice notice-error"><p>Application not found.</p></div>';
        echo '<p><a href="?page=rotary-applications" class="button">&larr; Back to List</a></p>';
        return;
    }
    
    ?>
    <div class="wrap">
        <h1>Application Details #<?php echo $app->id; ?></h1>
        
        <p>
            <a href="?page=rotary-applications" class="button">&larr; Back to List</a>
        </p>
        
        <div style="margin-top: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                    <h3 style="margin-top: 0;">Application Status</h3>
                    <p>
                        <span style="
                            background: <?php echo $app->status == 'pending' ? '#f39c12' : ($app->status == 'approved' ? '#27ae60' : '#e74c3c'); ?>;
                            color: white;
                            padding: 5px 15px;
                            border-radius: 3px;
                            font-size: 14px;
                            font-weight: bold;
                        ">
                            <?php echo ucfirst($app->status); ?>
                        </span>
                    </p>
                    <p><strong>Submission Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($app->submission_date)); ?></p>
                    <p><strong>Application ID:</strong> #<?php echo $app->id; ?></p>
                </div>
                
                <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                    <h3 style="margin-top: 0;">Contact Information</h3>
                    <p><strong>Name:</strong> <?php echo esc_html($app->name); ?></p>
                    <p><strong>Email:</strong> <?php echo esc_html($app->email); ?></p>
                    <p><strong>Mobile:</strong> <?php echo esc_html($app->mobile_no); ?></p>
                    <p><strong>Preferred Address:</strong> <?php echo ucfirst($app->preferred_address); ?></p>
                </div>
            </div>
            
            <!-- Application Details -->
            <div style="background: #fff; padding: 30px; border: 1px solid #ccd0d4; margin-bottom: 20px;">
                <h2>Complete Application Details</h2>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                    <!-- Main Information -->
                    <div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                            <!-- Personal Information -->
                            <div>
                                <h3>Personal Information</h3>
                                <table class="widefat" style="border: none;">
                                    <tr>
                                        <td width="40%" style="padding: 8px 0; font-weight: bold;">Name:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->name); ?></td>
                                    </tr>
                                    <?php if ($app->nickname): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Nickname:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->nickname); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Email:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->email); ?></td>
                                    </tr>
                                    <?php if ($app->business_email): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Business Email:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->business_email); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Mobile No:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->mobile_no); ?></td>
                                    </tr>
                                    <?php if ($app->business_tel): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Business Tel:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->business_tel); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($app->date_of_birth): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Date of Birth:</td>
                                        <td style="padding: 8px 0;"><?php echo date('F j, Y', strtotime($app->date_of_birth)); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            
                            <!-- Professional Information -->
                            <div>
                                <h3>Professional Information</h3>
                                <table class="widefat" style="border: none;">
                                    <?php if ($app->business_employer): ?>
                                    <tr>
                                        <td width="40%" style="padding: 8px 0; font-weight: bold;">Business/Employer:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->business_employer); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($app->current_position): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Current Position:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->current_position); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($app->industry_type): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Industry Type:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->industry_type); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($app->personal_assistant): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">Personal Assistant:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->personal_assistant); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($app->pa_email): ?>
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: bold;">PA Email:</td>
                                        <td style="padding: 8px 0;"><?php echo esc_html($app->pa_email); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div style="margin-top: 30px;">
                            <h3>Address Information</h3>
                            <table class="widefat" style="border: none; width: 100%;">
                                <tr>
                                    <td width="15%" style="padding: 8px 0; font-weight: bold; vertical-align: top;">Home Address:</td>
                                    <td style="padding: 8px 0;"><?php echo nl2br(esc_html($app->home_address)); ?></td>
                                </tr>
                                <?php if ($app->business_address): ?>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Business Address:</td>
                                    <td style="padding: 8px 0;"><?php echo nl2br(esc_html($app->business_address)); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        
                        <!-- Rotary Experience -->
                        <?php if ($app->previous_rotarian): ?>
                        <div style="margin-top: 30px;">
                            <h3>Previous Rotary Experience</h3>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
                                <?php echo nl2br(esc_html($app->previous_rotarian)); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Background Information -->
                        <div style="margin-top: 30px;">
                            <h3>Background Information</h3>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
                                <?php echo nl2br(esc_html($app->personal_background)); ?>
                            </div>
                        </div>
                        
                        <!-- Signatures -->
                        <div style="margin-top: 30px;">
                            <h3>Signatures</h3>
                            <table class="widefat" style="border: none; width: 100%;">
                                <tr>
                                    <td width="30%" style="padding: 8px 0; font-weight: bold;">Applicant Signature:</td>
                                    <td style="padding: 8px 0;"><?php echo esc_html($app->applicant_signature); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Application Date:</td>
                                    <td style="padding: 8px 0;"><?php echo date('F j, Y', strtotime($app->application_date)); ?></td>
                                </tr>
                                <?php if ($app->nominator_name): ?>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Nominated by:</td>
                                    <td style="padding: 8px 0;"><?php echo esc_html($app->nominator_name); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Nominator Signature:</td>
                                    <td style="padding: 8px 0;"><?php echo esc_html($app->nominator_signature); ?></td>
                                </tr>
                                <?php if ($app->board_approval_date): ?>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Board Approved on:</td>
                                    <td style="padding: 8px 0;"><?php echo date('F j, Y', strtotime($app->board_approval_date)); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Photo Section -->
                    <div>
                        <h3>Passport Photo</h3>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center;">
                            <?php if (!empty($app->photo_url)): ?>
                                <img src="<?php echo esc_url($app->photo_url); ?>" 
                                     alt="Passport Photo of <?php echo esc_attr($app->name); ?>" 
                                     style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; padding: 5px; background: white;">
                                <p style="margin-top: 10px;">
                                    <a href="<?php echo esc_url($app->photo_url); ?>" 
                                       target="_blank" 
                                       class="button button-small">
                                        View Full Size
                                    </a>
                                </p>
                            <?php else: ?>
                                <div style="background: #fff; padding: 40px; border: 2px dashed #ddd; text-align: center;">
                                    <span class="dashicons dashicons-format-image" style="font-size: 48px; color: #ccc;"></span>
                                    <p style="margin-top: 10px; color: #999;">No photo uploaded</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <h4>Photo Status</h4>
                            <p>
                                <strong>Photo Uploaded:</strong> 
                                <?php echo !empty($app->photo_url) ? 
                                    '<span style="color: #28a745;">Yes</span>' : 
                                    '<span style="color: #dc3545;">No</span>'; ?>
                            </p>
                            <?php if (!empty($app->photo_url)): ?>
                                <p><strong>Photo URL:</strong><br>
                                <small style="word-break: break-all;"><?php echo esc_html($app->photo_url); ?></small></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Update Form -->
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                <h3>Update Application Status</h3>
                <form method="post">
                    <?php wp_nonce_field('update_status', 'status_nonce'); ?>
                    <input type="hidden" name="app_id" value="<?php echo $app->id; ?>">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="status">Status</label></th>
                            <td>
                                <select name="status" id="status" style="width: 200px;">
                                    <option value="pending" <?php selected($app->status, 'pending'); ?>>Pending</option>
                                    <option value="approved" <?php selected($app->status, 'approved'); ?>>Approved</option>
                                    <option value="rejected" <?php selected($app->status, 'rejected'); ?>>Rejected</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <button type="submit" name="update_status" class="button button-primary">Update Status</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <?php
}

// AJAX handler for status updates
add_action('wp_ajax_rotary_update_status', 'rotary_ajax_update_status');
function rotary_ajax_update_status() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'rotary_status_update')) {
        wp_send_json_error(array('message' => 'Security check failed'));
    }
    
    // Check permissions
    if (!current_user_can('edit_pages')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
    }
    
    global $wpdb;
    $table_name = ROTARY_TABLE;
    
    $id = intval($_POST['id']);
    $status = sanitize_text_field($_POST['status']);
    
    // Validate status
    if (!in_array($status, array('pending', 'approved', 'rejected'))) {
        wp_send_json_error(array('message' => 'Invalid status'));
    }
    
    // Update status
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $id),
        array('%s'),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => 'Database error: ' . $wpdb->last_error));
    } else {
        wp_send_json_success(array('message' => 'Status updated successfully'));
    }
}

// Uninstall cleanup
register_uninstall_hook(__FILE__, 'rotary_uninstall');
function rotary_uninstall() {
    global $wpdb;
    $table_name = ROTARY_TABLE;
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}