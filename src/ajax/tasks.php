<?php

require_once(dirname(__FILE__) . "/../inc/load.php");

$QUERY = json_decode(file_get_contents('php://input'), true);

$ajax = new Ajax("tasks");
if (!isset($QUERY['action'])) {
  $ajax->setStatus(DAjaxStatus::ERROR, "No action set!");
  $ajax->send();
}
else if (!CSRF::check($QUERY['csrf'])) {
  $ajax->setStatus(DAjaxStatus::ERROR, "Invalid CSRF token!");
  $ajax->send();
}

$taskHandler = new TaskHandler();
$taskHandler->handle($QUERY['action'], $QUERY);

if (UI::getNumMessages(UI::ERROR)) {
  $ajax->setStatus(DAjaxStatus::ERROR);
  $ajax->addData(["messages" => UI::getArr()]);
}
else {
  $ajax->setStatus(DAjaxStatus::OK);
  $ajax->addData(["messages" => UI::getArr()]);
}
$ajax->send();
