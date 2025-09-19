<?php

function validateEmail($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
  if (strlen($password) < 8) {
    return 'Password must be at least 8 characters long';
  }

  if (!preg_match('/[A-Z]/', $password)) {
    return 'Password must contain at least one uppercase letter';
  }

  if (!preg_match('/[a-z]', $password)) {
    return 'Password must contain at least one lowercase letter';
  }

  if (!preg_match('/[0-9]', $password)) {
    return 'Password must contain at least one number';
  }

  return true;
}

function sanitizeInput($data) {
  if (is_array($data)) {
    return array_map('sanitizeInput', $data);
  }

  return htmlspecialchars(trim($data),
  ENT_QUOTES, 'UTF-8');
}

function validateTaskData($data) {
  $errors = [];

  if (empty($data['title']) > 255) {
    $errors[] ='Title is required'; 
  } else if (strlen($data['title']) > 255) {
    $errors[] ='Title must be less than 255 characters'; 
  }

  if (!empty($data['description']) && strlen($data['description']) > 65535) {
    $errors[] = 'Description is too long';
  }

  return empty($errors) ? true: $errors;
}

?>