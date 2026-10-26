<?php

$templateDocx = 'Accomplishment_Report_Sep16-30_2026.docx';
$outputDocx = 'Accomplishment_Report_Oct01-15_2026.docx';

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
    'for September 16-30, 2026',
    'for October 1-15, 2026',
    $xmlContent
);

// 2. Prepare new sections XML
$sections = [
    [
        'title' => 'External Portal:',
        'items' => [
            'Engineered Interactive Calendar & Duty Roster Management Engine (ScheduleCalendarController, schedule-calendar.js, 1,100+ lines): built full calendar scheduling UI with month/week/day views, custom drag/drop & click slot booking, dynamic shift selection modals, and real-time live synchronization.',
            'Designed and implemented Shift Preset Management Subsystem: engineered shift_presets schema migration and Filament resource (ShiftPresetsResource, ShiftPresetsTable, ShiftPresetsForm) supporting standard/custom hospital duty codes, break configurations, and color-coded schedule badges.',
            'Implemented Agency-Based Multi-Tenant Access Filtering: created database migration for administrator agency assignments and integrated scope filters across ExternalListsResource, ViewSchedule, and ViewUserDTR to restrict supervisors to their assigned external personnel.',
            'Refactored DTR and Live Schedule Administrative Views (AdministratorDTRView, DTRView, ViewScheduleAdminWidget) with dynamic duty assignment caching, optimized shift status resolution, and enhanced schedule query performance.',
        ]
    ],
    [
        'title' => 'Centralized Object Storage Service (COSS) & Recruitment System Integration:',
        'items' => [
            'Architected and deployed COSS (Centralized Object Storage Service) microservice: developed high-throughput REST API with chunked file uploads (InitiateChunkUploadRequest, UploadChunkSliceRequest), asynchronous ClamAV antivirus scanning queue (ScanUploadedFileJob), and strict HMAC-SHA256 client authentication.',
            'Engineered Zero-Trust Time-Limited & IP-Restricted Download Subsystem: developed dynamic signed URL generation with link expiration, download count quotas, optional password protection, and client IP whitelisting (FileDownloadRestrictionsTest).',
            'Built COSS Systems & File Governance Administration Portal (ManagementController, LinksManagement.jsx, QuarantineAndExpiredManagement.jsx) for real-time link tracking, quota inspection, and automated file purging (coss:purge-expired-files).',
            'Integrated COSS into Recruitment System: developed CossService client wrapper, automated background link synchronization command (coss:sync-links), and linked candidate attachment storage for credentials, resumes, and examination records.',
            'Engineered Concurrent Exam Security Controls: implemented strict single-session checks preventing concurrent examinee logins, active exam session monitoring, and real-time exam notification alerts (ApplicantSingleSessionTest, exam-bell-notification.blade.php).',
        ]
    ],
    [
        'title' => 'AttendanceQR & DTRservice Modules:',
        'items' => [
            'Architected Automated Flag Ceremony Attendance Generation Subsystem (CreateFlagAttendanceCommand, run_attendance_create.vbs): scheduled automated morning ceremony attendance generation with real-time audit logging and console dispatching.',
            'Overhauled Mobile QR Scanner & Geolocation Verification Interface: integrated Google Maps calibration engine (Calibrate.jsx, use-google-maps.js), real-time camera QR decoder with retry fallbacks (QRScannerModal.jsx), and enhanced location-boundary restriction feedback (notinLocation.jsx).',
            'Isolated HRBLIZ Biometric Device Transaction Pipeline: engineered dedicated device_logs_hrbliz schema migration, separate repository data models, and dedicated log alert view controllers to cleanly segregate HRBLIZ standalone terminal logs from primary hospital biometric streams.',
            'Enhanced Physical Terminal Diagnostics & Command Pipeline: developed comprehensive device operation logging service (OperationLogger, DeviceOperationLogsTest) and automated sent command recovery handling in DeviceCommandService.',
        ]
    ]
];

$doc = new DOMDocument();
$doc->loadXML($xmlContent);
$xpath = new DOMXPath($doc);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$body = $doc->getElementsByTagName('body')->item(0);

// Find first header <w:p> containing "Recruitment System:"
// and find <w:p> containing "Other related tasks:"
$firstHeaderP = null;
$otherTasksHeaderP = null;

$paragraphs = $xpath->query('//w:p');
foreach ($paragraphs as $p) {
    $t = trim($p->textContent);
    if ($t === 'Recruitment System:') {
        $firstHeaderP = $p;
    }
    if ($t === 'Other related tasks:') {
        $otherTasksHeaderP = $p;
    }
}

if (!$firstHeaderP || !$otherTasksHeaderP) {
    die("Could not find anchor paragraphs.\n");
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

echo "Successfully regenerated Accomplishment_Report_Oct01-15_2026.docx cleanly via DOMDocument!\n";
