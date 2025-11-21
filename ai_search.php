<!DOCTYPE html>
<html lang="ms">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta charset="UTF-8" />
  <title>AI Search – UTHM Archive (FYP)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Poppins', 'Segoe UI', sans-serif;
      font-size: 13px; /* lebih kecil sikit */
    }

    /* Untuk potong teks supaya 2 line saja */
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-6">

  <!-- HEADER SECTION -->
  <header class="text-center mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-[#0d3b66] mb-1">
      UTHM Institutional Repository
    </h1>
    <p class="text-gray-600 max-w-2xl mx-auto">
      ✨ Temui <span class="font-medium text-[#0d3b66]">projek, tesis dan artikel pelajar UTHM</span> dengan carian AI pintar kami!  
      Sistem ini disambungkan terus ke <span class="font-semibold">Repositori DSpace-CRIS UTHM</span>,  
      dan akan gunakan <span class="italic">data tempatan</span> jika capaian luar tidak tersedia.
    </p>
  </header>

  <!-- SEARCH BAR -->
  <div class="w-full max-w-2xl bg-white rounded-xl shadow-md p-4 mb-8 border border-gray-100">
    <div class="flex flex-col sm:flex-row gap-2">
      <input id="searchInput" 
             type="text" 
             placeholder="🔍 Taip tajuk, penulis, atau kata kunci..."
             class="flex-grow border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b66]" />
      <button id="btnSearch" 
              class="bg-[#0d3b66] hover:bg-[#0a2f54] text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
        Cari
      </button>
    </div>
    <p class="text-xs text-gray-500 mt-2 italic text-center">
      💡 Tekan <span class="font-semibold">Enter</span> untuk carian pantas.
    </p>
  </div>

  <!-- RESULTS SECTION (NOW 4 COLUMNS) -->
  <div id="results" class="w-full max-w-6xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3"></div>

<script>
const input = document.getElementById('searchInput');
const btn = document.getElementById('btnSearch');
const resultsDiv = document.getElementById('results');

async function searchData() {
  const q = input.value.trim();
  if (!q) {
    resultsDiv.innerHTML = `
      <div class="bg-white p-4 rounded-xl shadow text-center text-gray-500 col-span-full">
        ⚠️ Sila masukkan kata kunci carian.
      </div>`;
    return;
  }

  // Loading UI
  resultsDiv.innerHTML = `
    <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3 animate-pulse col-span-full">
      <svg class="w-6 h-6 text-[#0d3b66]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16" />
      </svg>
      <div>
        <p class="text-[#0d3b66] font-medium text-sm">🔎 Mengakses DSpace-CRIS Repository...</p>
        <p class="text-gray-500 text-xs">Mencari "<span class="font-semibold">${q}</span>" — jika lambat, sistem akan guna data cache tempatan.</p>
      </div>
    </div>`;

  try {
    const res = await fetch(`fetch_archive.php?q=${encodeURIComponent(q)}`);
    if (!res.ok) throw new Error('Network error');
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) {
      resultsDiv.innerHTML = `
        <div class="bg-white p-5 rounded-xl shadow text-center col-span-full">
          <p class="text-red-500 font-medium text-sm">❌ Tiada hasil dijumpai.</p>
          <p class="text-gray-500 text-xs mt-1">Cuba gunakan kata kunci lain.</p>
        </div>`;
      return;
    }

    // Display results in compact 4-column grid
    resultsDiv.innerHTML = data.map(item => `
      <div class="bg-white p-3 rounded-lg shadow hover:shadow-md border border-gray-100 transition flex flex-col justify-between">
        <div>
          <a href="${item.link}" target="_blank" 
             class="text-[#0d3b66] font-semibold text-sm leading-tight hover:underline line-clamp-2">
             ${item.title}
          </a>
          <p class="text-gray-500 text-[11px] mt-1 line-clamp-2">
            ${item.meta ?? 'Sumber: UTHM Archive'}
          </p>
        </div>

        <div class="flex gap-1 mt-3">
          <a href="${item.link}" target="_blank"
            class="text-[11px] w-1/2 text-center px-2 py-1 rounded border border-[#0d3b66] text-[#0d3b66] hover:bg-[#f0f4fa] transition">
            Buka
          </a>
          <a href="/fyp_system/project_details.php?link=${encodeURIComponent(item.link)}"
            class="text-[11px] w-1/2 text-center px-2 py-1 rounded bg-[#0d3b66] text-white hover:bg-[#0a2f54] transition">
            📄 Butiran
          </a>
        </div>
      </div>
    `).join('');

  } catch (err) {
    resultsDiv.innerHTML = `
      <div class="bg-white p-5 rounded-xl shadow text-center col-span-full">
        <p class="text-red-500 font-medium text-sm">⚠️ Ralat semasa mengakses data. Menggunakan cache tempatan jika ada.</p>
      </div>`;
  }
}

// Event listeners
btn.addEventListener('click', searchData);
input.addEventListener('keydown', (e) => { if (e.key === 'Enter') searchData(); });
</script>
</body>
</html>
