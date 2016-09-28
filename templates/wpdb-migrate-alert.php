<?php 

  $sMessageType = $aAlert['type'];
  $sMessage     = $aAlert['message'];

  switch ($sMessageType) {
    case "success":
      $sMessageStyle = "background-color: #80dc80; color: #106510; border-color: #106510;";
      break;
    case "warning":
      $sMessageStyle = "background-color: #f7e673; color: #82720c; border-color: #82720c;";
      break;
    default:
      break;
  }

if(!empty($sMessage)): ?>
  <div style="width:90%; margin: auto; display: table;">
    <div style="border: 1px solid #333; padding: 14px; border-radius: 3px; margin-bottom: 10px;<?php echo !empty($sMessageStyle) ? $sMessageStyle : ""; ?>">
      <p style="margin: 0; font-size: 14px; line-height: 1em;"><?php echo $sMessage; ?></p>
    </div>
  </div><?php
endif; ?>