<?php
$id = basename($_SERVER['REQUEST_URI']);
if (!$id || !preg_match('/^[a-z0-9]{8}$/', $id)) {
    http_response_code(404); echo 'Kad tidak dijumpai.'; exit;
}
$dir = __DIR__ . '/';
$jsonFile = $dir . $id . '.json';
if (!file_exists($jsonFile)) {
    http_response_code(404); echo 'Kad tidak dijumpai.'; exit;
}
$data = json_decode(file_get_contents($jsonFile), true);
$photo = htmlspecialchars($data['photo']);
$wish = $data['wish'];
$signature = $data['signature'];
$theme = $data['theme'];
$themeStyles = [
    'theme1' => 'background:#f8f5f0;',
    'theme2' => 'background:#e0f7fa;',
    'theme3' => 'background:#fffbe6;'
];
?><!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kad Raya Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { min-height:100vh; font-family:'Lora',serif; <?= $themeStyles[$theme] ?? $themeStyles['theme1'] ?> }
    .ekad-card { background:rgba(255,255,255,0.92); border-radius:2rem; box-shadow:0 8px 32px 0 rgba(27,67,50,0.12); padding:2rem 1.2rem 1.5rem 1.2rem; max-width:400px; width:100%; border:3px solid #cfb53b; position:relative; overflow:hidden; margin:2.5rem auto 1.5rem auto; }
    .greeting { font-family:'Great Vibes',cursive; color:#1b4332; font-size:2.1rem; text-align:center; margin-bottom:0.2rem; }
    .sub-greeting { font-size:1.1rem; color:#cfb53b; text-align:center; font-family:'Lora',serif; margin-bottom:1.2rem; }
    .family-photo { display:flex; justify-content:center; margin-bottom:1.2rem; }
    .family-photo img { width:220px; height:220px; object-fit:cover; border-radius:1.2rem; border:5px solid #cfb53b; box-shadow:0 4px 16px 0 rgba(207,181,59,0.12); background:#fff; }
    .message-box { background:rgba(27,67,50,0.08); border-radius:1rem; padding:1.1rem 1rem 1rem 1rem; margin-bottom:1.1rem; position:relative; text-align:center; font-size:1.05rem; color:#1b4332; font-family:'Lora',serif; display:flex; align-items:center; justify-content:center; gap:0.7rem; }
    .message-icon { width:32px; height:32px; opacity:0.85; }
    .signature { text-align:center; font-size:1rem; color:#1b4332; margin-bottom:0.7rem; font-family:'Lora',serif; }
    @media (max-width:600px) { .ekad-card { padding:1.1rem 0.2rem 1rem 0.2rem; max-width:100vw; border-radius:1.1rem; margin:1.2rem 0.2rem 1.2rem 0.2rem; } .family-photo img { width:98vw; max-width:170px; height:170px; } }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="ekad-card w-100">
      <div class="greeting">Selamat Hari Raya Aidilfitri</div>
      <div class="sub-greeting">Maaf Zahir &amp; Batin</div>
      <div class="family-photo">
        <img src="<?= $photo ?>" alt="Family Photo">
      </div>
      <div class="message-box">
        <span class="message-icon">
          <svg viewBox="0 0 32 32" fill="none"><ellipse cx="16" cy="14" rx="10" ry="14" stroke="#cfb53b" stroke-width="2" fill="#fffbe6"/><rect x="13" y="2" width="6" height="4" rx="2" fill="#cfb53b"/><rect x="15" y="0" width="2" height="4" rx="1" fill="#1b4332"/><rect x="15" y="28" width="2" height="4" rx="1" fill="#1b4332"/></svg>
        </span>
        <span style="flex:1;"> <?= $wish ?> </span>
        <span class="message-icon">
          <svg viewBox="0 0 32 32" fill="none"><ellipse cx="16" cy="22" rx="10" ry="6" stroke="#cfb53b" stroke-width="2" fill="#fffbe6"/><rect x="13" y="8" width="6" height="14" rx="3" fill="#cfb53b"/><path d="M16 2 Q18 6 16 8 Q14 6 16 2 Z" fill="#1b4332"/></svg>
        </span>
      </div>
      <div class="signature">Daripada: <?= $signature ?></div>
    </div>
  </div>
  <!-- Comment Box Section -->
  <div class="container d-flex justify-content-center mt-3 mb-4">
    <div class="w-100" style="max-width:400px;">
      <form id="wishForm" class="bg-white p-3 rounded shadow-sm" style="border:1px solid #cfb53b;">
        <label for="wishInput" class="form-label" style="color:#1b4332;font-weight:bold;">Tinggalkan Ucapan Anda:</label>
        <textarea class="form-control mb-2" id="wishInput" rows="2" maxlength="200" placeholder="Contoh: Selamat Hari Raya! Maaf zahir & batin."></textarea>
        <button type="submit" class="btn btn-success w-100" style="background:#1b4332;border:none;">Hantar Ucapan</button>
      </form>
      <div id="wishDisplay" class="mt-3" style="display:none;">
        <div class="alert alert-success" role="alert" style="background:#fffbe6;border:1px solid #cfb53b;color:#1b4332;">
          <strong>Ucapan anda:</strong>
          <div id="wishMessage" style="margin-top:0.5rem;"></div>
        </div>
      </div>
      <div id="allWishes" class="mt-4"></div>
    </div>
  </div>
  <script>
    // Comment box logic with backend JSON storage per kad
    function renderAllWishes(wishes) {
      var allWishesDiv = document.getElementById('allWishes');
      if (!wishes || wishes.length === 0) {
        allWishesDiv.innerHTML = '';
        return;
      }
      let html = '<div class="bg-white rounded shadow-sm p-2" style="border:1px solid #cfb53b;">';
      html += '<div style="color:#1b4332;font-weight:bold;margin-bottom:0.5rem;">Ucapan yang telah diterima:</div>';
      wishes.slice().reverse().forEach(function(w) {
        html += '<div class="mb-2" style="border-bottom:1px dashed #cfb53b;padding-bottom:0.3rem;">';
        html += '<span style="color:#1b4332;">' + w.wish + '</span>';
        html += '<div style="font-size:0.8em;color:#888;">' + w.time + '</div>';
        html += '</div>';
      });
      html += '</div>';
      allWishesDiv.innerHTML = html;
    }

    function loadWishes() {
      fetch('save_wish.php?id=<?= $id ?>')
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            renderAllWishes(data.wishes);
          }
        });
    }

    document.getElementById('wishForm').addEventListener('submit', function(e) {
      e.preventDefault();
      var wish = document.getElementById('wishInput').value.trim();
      if (wish.length > 0) {
        fetch('save_wish.php?id=<?= $id ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ wish: wish })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById('wishMessage').textContent = wish;
            document.getElementById('wishDisplay').style.display = 'block';
            document.getElementById('wishForm').reset();
            renderAllWishes(data.wishes);
          } else {
            document.getElementById('wishMessage').textContent = '';
            document.getElementById('wishDisplay').style.display = 'none';
          }
        });
      } else {
        document.getElementById('wishMessage').textContent = '';
        document.getElementById('wishDisplay').style.display = 'none';
      }
    });

    // Load wishes on page load
    loadWishes();
  </script>
</body>
</html>
