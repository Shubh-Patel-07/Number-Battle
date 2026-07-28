<?php
require __DIR__.'/config.php';
$action = $_GET['action'] ?? '';
$body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
try {
  if ($action === 'register') {
    $username=trim($body['username']??''); $email=filter_var($body['email']??'',FILTER_VALIDATE_EMAIL); $password=$body['password']??'';
    if (strlen($username)<3 || strlen($username)>24 || !$email || strlen($password)<8) respond(['error'=>'Enter a username, valid email, and 8+ character password.'],422);
    $s=db()->prepare('INSERT INTO users(username,email,password_hash) VALUES(?,?,?)'); $s->execute([$username,$email,password_hash($password,PASSWORD_DEFAULT)]);
    $_SESSION['user']=['id'=>(int)db()->lastInsertId(),'username'=>$username]; respond(['user'=>$_SESSION['user']]);
  }
  if ($action === 'login') {
    $s=db()->prepare('SELECT id,username,password_hash FROM users WHERE email=?'); $s->execute([strtolower(trim($body['email']??''))]); $u=$s->fetch();
    if (!$u || !password_verify($body['password']??'', $u['password_hash'])) respond(['error'=>'Invalid email or password.'],401);
    $_SESSION['user']=['id'=>(int)$u['id'],'username'=>$u['username']]; respond(['user'=>$_SESSION['user']]);
  }
  if ($action === 'logout') { session_destroy(); respond(['ok'=>true]); }
  $me=require_user();
  if ($action === 'me') respond(['user'=>$me]);
  if ($action === 'create') {
    do {$code=room_code(); $q=db()->prepare('SELECT id FROM rooms WHERE code=?');$q->execute([$code]);} while($q->fetch());
    db()->prepare('INSERT INTO rooms(code,host_id) VALUES(?,?)')->execute([$code,$me['id']]); $id=(int)db()->lastInsertId(); db()->prepare('INSERT INTO room_players(room_id,user_id) VALUES(?,?)')->execute([$id,$me['id']]); respond(['code'=>$code]);
  }
  $code=strtoupper(trim($body['code']??$_GET['code']??'')); $q=db()->prepare('SELECT * FROM rooms WHERE code=?');$q->execute([$code]);$room=$q->fetch(); if(!$room)respond(['error'=>'Room not found.'],404);
  $member=db()->prepare('SELECT * FROM room_players WHERE room_id=? AND user_id=?');$member->execute([$room['id'],$me['id']]);
  if ($action === 'join' && !$member->fetch()) { $count=(int)db()->query('SELECT COUNT(*) FROM room_players WHERE room_id='.(int)$room['id'])->fetchColumn(); if($count>=2)respond(['error'=>'Room is full.'],409); db()->prepare('INSERT INTO room_players(room_id,user_id) VALUES(?,?)')->execute([$room['id'],$me['id']]); }
  if ($action === 'ready') { if(!valid_number($body['secret']??''))respond(['error'=>'Use four different digits.'],422); db()->prepare('UPDATE room_players SET secret=?,ready_at=NOW() WHERE room_id=? AND user_id=?')->execute([$body['secret'],$room['id'],$me['id']]); $ready=(int)db()->query('SELECT COUNT(*) FROM room_players WHERE room_id='.(int)$room['id'].' AND ready_at IS NOT NULL')->fetchColumn(); if($ready===2){$first=db()->query('SELECT user_id FROM room_players WHERE room_id='.(int)$room['id'].' ORDER BY user_id LIMIT 1')->fetchColumn();db()->prepare("UPDATE rooms SET status='playing',turn_user_id=? WHERE id=?")->execute([$first,$room['id']]);} }
  if ($action === 'guess') { $guess=$body['guess']??''; if(!valid_number($guess))respond(['error'=>'Use four different digits.'],422); $q=db()->prepare('SELECT * FROM rooms WHERE id=?');$q->execute([$room['id']]);$room=$q->fetch();if($room['status']!=='playing'||(int)$room['turn_user_id']!==$me['id'])respond(['error'=>'It is not your turn.'],409);$s=db()->prepare('SELECT secret,user_id FROM room_players WHERE room_id=? AND user_id<>?');$s->execute([$room['id'],$me['id']]);$opponent=$s->fetch();$result=score($opponent['secret'],$guess);db()->prepare('INSERT INTO guesses(room_id,user_id,value,correct_digits,correct_positions) VALUES(?,?,?,?,?)')->execute([$room['id'],$me['id'],$guess,$result['correctDigits'],$result['correctPositions']]);if($result['correctPositions']===4){db()->prepare("UPDATE rooms SET status='finished',winner_id=? WHERE id=?")->execute([$me['id'],$room['id']]);db()->prepare('UPDATE users SET wins=wins+1,xp=xp+100 WHERE id=?')->execute([$me['id']]);db()->prepare('UPDATE users SET losses=losses+1 WHERE id=?')->execute([$opponent['user_id']]);}else db()->prepare('UPDATE rooms SET turn_user_id=? WHERE id=?')->execute([$opponent['user_id'],$room['id']]); }
  if ($action === 'chat') { $text=trim(substr($body['text']??'',0,500));if($text)db()->prepare('INSERT INTO messages(room_id,user_id,text) VALUES(?,?,?)')->execute([$room['id'],$me['id'],$text]); }
  if ($action === 'state' || $action==='join' || $action==='ready' || $action==='guess' || $action==='chat') { $q=db()->prepare('SELECT r.*,u.username winner_name FROM rooms r LEFT JOIN users u ON r.winner_id=u.id WHERE r.id=?');$q->execute([$room['id']]);$r=$q->fetch();$p=db()->prepare('SELECT rp.user_id,u.username,rp.ready_at IS NOT NULL ready FROM room_players rp JOIN users u ON u.id=rp.user_id WHERE room_id=?');$p->execute([$room['id']]);$g=db()->prepare('SELECT g.*,u.username FROM guesses g JOIN users u ON u.id=g.user_id WHERE room_id=? ORDER BY g.id DESC');$g->execute([$room['id']]);$m=db()->prepare('SELECT m.*,u.username FROM messages m JOIN users u ON u.id=m.user_id WHERE room_id=? ORDER BY m.id DESC LIMIT 30');$m->execute([$room['id']]);respond(['room'=>$r,'players'=>$p->fetchAll(),'guesses'=>$g->fetchAll(),'messages'=>array_reverse($m->fetchAll()),'me'=>$me]); }
  respond(['error'=>'Unknown action.'],404);
} catch (PDOException $e) { respond(['error'=>'Database problem. Check config.php and import database.sql.'],500); }
