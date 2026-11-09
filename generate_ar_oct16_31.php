<?php

$templateDocx = 'Accomplishment_Report_Oct01-15_2026.docx';
$outputDocx = 'Accomplishment_Report_Oct16-31_2026.docx';

if (!copy($templateDocx, $outputDocx)) {
    die("Failed to copy template docx.\n");
}

$zip = new ZipArchive();
if ($zip->open($outputDocx) !== true) {
    die("Failed to open output docx with ZipArchive.\n");
}

$xmlContent = $zip->getFromName('word/document.xml');
if (!$xmlContent) {
    die("Failed to read word/document.xml from docx.\n");
}

// 1. Update the date header
$xmlContent = str_replace(
    'for October 1-15, 2026',
    'for October 16-31, 2026',
    $xmlContent
);

// 2. Prepare new sections XML
$sections = [
    [
        'title' => 'UMIS Landing Page & UMIS Portal Client (umis-landing-page & zcmc-portal-client):',
        'items' => [
            'Integrated Human Milk Bank (HMB) System Card: deployed new portal access card on landing page with automated single sign-on (SSO) token handoff, environment-aware routing (getHmbUrl), and role-based permissions.',
            'Engineered Time-Based One-Time Password (TOTP) / Google Authenticator Portal Challenge: enhanced portal authentication flow (SignInWithOTP, OTPVerification) with auto-advancing 6-digit TOTP input, emergency recovery code fallback, lockout countdown timers, and client-side payload protection (PayloadSecurity.js).',
            'Implemented Interactive Google Authenticator Account Security Modal (TwoFactorSettingsModal.jsx, AccountHook.jsx, ManageAccount.jsx): built complete self-service 2FA enrollment UI allowing hospital staff to scan QR codes, copy manual keys, verify tokens, and securely download backup codes.',
        ]
    ],
    [
        'title' => 'UMIS Backend Microservices (user_management_server):',
        'items' => [
            'Architected Two-Factor Authentication (2FA) Subsystem: developed GAuthClientService and TwoFactorAuthController interfacing with GAuth microservice for TOTP secret generation, QR rendering, code validation, and recovery code consumption.',
            'Engineered Database Migration & Credential Pipeline Integration: added two_factor_confirmed_at schema migration to employee_profiles table and integrated mandatory 2FA challenge and enforcement within AuthWithCredentialController and EmployeeProfileController.',
            'Refactored DTR Attendance Endpoints & API Gateway Compatibility: optimized AttendanceController data access patterns, streamlined CORS configurations, and improved biometric attendance retrieval response times.',
        ]
    ],
    [
        'title' => 'Recruitment System:',
        'items' => [
            'Engineered Applicant Individual Exam Timer Subsystem: implemented individual_timer_to_application_exams_table schema migration, upgraded ApplicationExam model and ExamController to support individualized examinee time limits, dynamic countdown enforcement, and full test suite validation (ExamIndividualTimerTest.php).',
            'Implemented Attachment Expiry Audit Engine (Application.php): developed automated validation to detect expired Centralized Object Storage Service (COSS) download links for candidate resumes, transcripts, and eligibility files.',
            'Optimized Recruitment Scheduling & Candidate Filtering: refactored calendar scheduling engine (CalendarSchedules.php, calendar.js, viewJobPostHeader.blade.php) and resolved filtering edge cases across candidate overview and rejection widgets (ListApplications, AppRejectedTableWidget, CandidatesOverviewTableWidget).',
            'Decoupled Exam Migration Constraints: resolved foreign key schema restrictions on applications table to enable seamless applicant re-examination workflows and template reusability.',
        ]
    ],
    [
        'title' => 'Material Management System (MMS):',
        'items' => [
            'Architected Enterprise MMS Core Architecture: designed and implemented Clean Architecture foundation utilizing Repository-Service Pattern (BaseRepositoryInterface, UserRepositoryInterface, BaseService, UserService, ApiResponseTrait, Filterable).',
            'Integrated Authentication & Security Subsystems: scaffolded Laravel Fortify authentication layer paired with Inertia.js (React/TypeScript), implementing user profile management, password updates, and two-factor authentication controls (SecurityController, ProfileController).',
            'Authored Comprehensive Architecture Documentation & Test Suite: developed ARCHITECTURE.md and implementation guides, alongside complete Pest/PHPUnit test suites covering architectural compliance, user authentication, and profile security.',
        ]
    ],
    [
        'title' => 'External Portal:',
        'items' => [
            'Implemented Google Authenticator Two-Factor Authentication: engineered TwoFactorLoginController, TwoFactorSettingsController, and GAuthClientService enforcing 2FA security protocols for external employees and supervisors.',
            'Engineered Database Schema & Security Migrations: created migration adding two_factor_secret, two_factor_recovery_codes, and two_factor_confirmed_at columns across external_employees and administrators tables.',
            'Designed Responsive 2FA Verification & Setup UI: built custom Blade templates (two-factor-setup.blade.php, two-factor-challenge.blade.php, scratch-styles.blade.php) and verified system reliability with automated feature tests (GoogleAuthenticatorTest.php).',
            'Resolved QR Code Display & Layout Anomalies: corrected QR code styling and responsive layout alignment in account settings to streamline user onboarding.',
        ]
    ],
    [
        'title' => 'DTRService & Biometric Integration:',
        'items' => [
            'Architected ZKTeco Biometric Reverse Entry Punch Synchronization Engine: developed ZkPunchForwarderService, ZkSyncPunches command, and UmisPunchClassifierService to reliably ingest, classify, and forward raw device punches into UMIS.',
            'Engineered Unified Device Logs REST API Pipeline: developed DeviceLogController supporting high-volume batch punch ingestion, secure API authentication, and verified integration via automated test suites (DeviceLogApiTest.php, DEVICE_LOGS_API_INTEGRATION_GUIDE.md).',
            'Calibrated Duty Schedule & Break Threshold Computations: adjusted automated break detection interval from 3.00 hours to 3.50 hours in DtrReportRepository and TimeRecordService, resolving DTR presentation anomalies and undertime calculation discrepancies (DtrBreakScheduleTest, DtrUndertimeComputationTest).',
            'Ensured High-Fidelity Punch Status Resolution: hardened device punch insertion routines to guarantee accurate punch state classification (time-in, time-out, break) across multi-terminal network setups.',
        ]
    ]
];

$doc = new DOMDocument();
$doc->loadXML($xmlContent);
$xpath = new DOMXPath($doc);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$firstHeaderP = null;
$otherTasksHeaderP = null;

$paragraphs = $xpath->query('//w:p');
foreach ($paragraphs as $p) {
    $t = trim($p->textContent);
    if ($t === 'External Portal:' || $t === 'Recruitment System:') {
        $firstHeaderP = $p;
        break;
    }
}

foreach ($paragraphs as $p) {
    $t = trim($p->textContent);
    if ($t === 'Other related tasks:') {
        $otherTasksHeaderP = $p;
        break;
    }
}

if (!$firstHeaderP || !$otherTasksHeaderP) {
    die("Could not find anchor paragraphs (firstHeaderP: " . ($firstHeaderP ? 'found' : 'not found') . ", otherTasksHeaderP: " . ($otherTasksHeaderP ? 'found' : 'not found') . ").\n");
}

// Remove paragraphs from $firstHeaderP up to (but not including) $otherTasksHeaderP
$curr = $firstHeaderP;
while ($curr && !$curr->isSameNode($otherTasksHeaderP)) {
    $next = $curr->nextSibling;
    $curr->parentNode->removeChild($curr);
    $curr = $next;
}

// Insert new sections right before $otherTasksHeaderP
foreach ($sections as $section) {
    // Header paragraph
    $headerFrag = $doc->createDocumentFragment();
    $hXml = '<w:p xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
          . '<w:pPr><w:spacing w:before="60" w:after="40" w:line="240" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="20"/></w:rPr></w:pPr>'
          . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="20"/></w:rPr>'
          . '<w:t xml:space="preserve">' . htmlspecialchars($section['title'], ENT_XML1, 'UTF-8') . '</w:t></w:r>'
          . '</w:p>';
    $headerFrag->appendXML($hXml);
    $otherTasksHeaderP->parentNode->insertBefore($headerFrag, $otherTasksHeaderP);

    // Bullet items
    foreach ($section['items'] as $item) {
        $itemFrag = $doc->createDocumentFragment();
        $bXml = '<w:p xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
              . '<w:pPr><w:ind w:left="360" w:hanging="240"/><w:spacing w:before="0" w:after="40" w:line="240" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="19"/></w:rPr></w:pPr>'
              . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="19"/></w:rPr><w:t xml:space="preserve">   </w:t></w:r>'
              . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="19"/></w:rPr><w:t xml:space="preserve">' . htmlspecialchars($item, ENT_XML1, 'UTF-8') . '</w:t></w:r>'
              . '</w:p>';
        $itemFrag->appendXML($bXml);
        $otherTasksHeaderP->parentNode->insertBefore($itemFrag, $otherTasksHeaderP);
    }
}

$updatedXml = $doc->saveXML();

$zip->deleteName('word/document.xml');
$zip->addFromString('word/document.xml', $updatedXml);
$zip->close();

echo "Successfully generated Accomplishment_Report_Oct16-31_2026.docx cleanly via DOMDocument!\n";
