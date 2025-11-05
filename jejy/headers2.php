<!-- ======= MINIMAL HEADER (Logo + Help) — paste this block ======= -->

<style>
  /* Hide old multi-row headers */
  .header_top, .header_middle { display:none !important; }

  :root{
    --brand:#0b66c3;   /* blue */
    --ink:#0f172a;
    --border:#e5e7eb;
  }

  /* Mini header */
  .mini-header{
    position:sticky; top:0; z-index:1000;
    background:#fff; border-bottom:1px solid var(--border);
  }
  .mh-container{
    max-width:1200px; margin:0 280px; height:90px;
    display:flex; align-items:center; justify-content:space-between;
    padding:0 12px;
  }
  .mh-logo img{ height:50px; width:auto; display:block; }

  /* Help button (icon + label) */
  .mh-help{ position:relative; display:flex; align-items:center; }
  .mh-help-btn{
    display:inline-flex; align-items:center; gap:8px;
    padding:8px 12px;
    background:transparent;
    border:0 !important; border-radius:999px;
    color:var(--brand); font-weight:600; text-transform:none;
    line-height:1; cursor:pointer;
  }
  .mh-help-btn:hover{ background:rgba(11,102,195,.08); }
  .mh-help-btn:focus-visible{ outline:3px solid rgba(11,102,195,.35); outline-offset:2px; }

  .mh-help-btn svg{
    width:22px; height:22px; display:block; stroke:var(--brand);
  }

  /* Dropdown menu */
  .mh-menu{
    position:absolute; right:0; top:calc(100% + 8px); min-width:220px;
    background:#fff; border:1px solid var(--border); border-radius:12px;
    box-shadow:0 12px 24px rgba(2,8,23,.12);
    padding:8px; display:none;
  }
  .mh-help.open .mh-menu{ display:block; }
  .mh-menu a{
    display:flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:8px; color:var(--ink); text-decoration:none;
  }
  .mh-menu a:hover{ background:#f1f5f9; }
  .mh-menu hr{ border:none; border-top:1px solid var(--border); margin:6px 0; }

  @media (max-width:600px){
    background:#fff; border-bottom:1px solid #fff;
  }
                   
                   
                   
/* ========== Contact Modal ========== */
:root{ --brand:#0b66c3; --ink:#0f172a; --border:#e5e7eb; --muted:#64748b; }

.modal{ position:fixed; inset:0; display:none; z-index:100000 !important; }
.modal.is-open{ display:block; }
.modal__backdrop{ position:absolute; inset:0; background:rgba(2,8,23,.45); }

.modal__dialog{
  position:relative; z-index:1; width:min(720px, 92vw);
  margin:8vh auto; background:#fff; border-radius:16px;
  border:1px solid var(--border); box-shadow:0 20px 40px rgba(2,8,23,.18);
  padding:18px 18px 22px;
}

.modal__header{ display:flex; align-items:center; justify-content:space-between; gap:12px; }
.modal__title{ font-size:20px; font-weight:700; color:var(--ink); margin:0; }
.modal__close{
  border:0; background:#fff; width:36px; height:36px; border-radius:10px;
  display:grid; place-items:center; cursor:pointer;
}
.modal__close:hover{ background:#f1f5f9; }

.modal__grid{
  margin-top:12px;
  display:grid; grid-template-columns:1fr 1fr; gap:14px;
}
@media (max-width:640px){ .modal__grid{ grid-template-columns:1fr; } }

.contact-card{
  display:flex; gap:12px; align-items:flex-start;
  padding:12px; border:1px solid #eef2f7; border-radius:12px; background:#fff;
}
.icon-circle{
  flex:0 0 44px; width:44px; height:44px; border-radius:999px;
  display:grid; place-items:center; border:1px solid #dbe3ec; background:#fff;
}
.icon-circle svg{ width:22px; height:22px; stroke:var(--brand); }

.contact-body .label{ font-weight:700; color:var(--ink); margin-bottom:4px; }
.contact-body a{ color:var(--brand); text-decoration:none; display:block; }
.contact-body a:hover{ text-decoration:underline; }

.contact-docs{
  grid-column:1 / -1; padding:12px; border:1px dashed #dbe3ec; border-radius:12px;
}
.contact-docs .label{ font-weight:700; margin-bottom:6px; color:var(--ink); }
.docs-slot{ min-height:80px; color:var(--muted); display:flex; align-items:center; justify-content:center; }

 /* Hide old multi-row headers */
.header_top, .header_middle { display:none !important; }

:root{
  --brand:#0b66c3;
  --ink:#0f172a;
  --border:#e5e7eb;
  --mh-h: 90px; /* default header height */
}

/* Mini header: sticky on desktop */
.mini-header{
  position: sticky;
  top: 0;
  z-index: 1000;
  background:#fff;
  border-bottom:1px solid var(--border);
}

.mh-logo img{ height:50px; width:auto; display:block; }

/* Help button (icon + label) */
.mh-help{ position:relative; display:flex; align-items:center; }
.mh-help-btn{
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 12px; background:transparent; border:0 !important; border-radius:999px;
  color:var(--brand); font-weight:600; line-height:1; cursor:pointer;
}
.mh-help-btn:hover{ background:rgba(11,102,195,.08); }
.mh-help-btn:focus-visible{ outline:3px solid rgba(11,102,195,.35); outline-offset:2px; }
.mh-help-btn svg{ width:22px; height:22px; display:block; stroke:var(--brand); }

/* Dropdown */
.mh-menu{
  position:absolute; right:0; top:calc(100% + 8px); min-width:220px;
  background:#fff; border:1px solid var(--border); border-radius:12px;
  box-shadow:0 12px 24px rgba(2,8,23,.12);
  padding:8px; display:none;
}
.mh-help.open .mh-menu{ display:block; }
.mh-menu a{
  display:flex; align-items:center; gap:10px;
  padding:10px 12px; border-radius:8px; color:var(--ink); text-decoration:none;
}
.mh-menu a:hover{ background:#f1f5f9; }
.mh-menu hr{ border:none; border-top:1px solid var(--border); margin:6px 0; }

@media (max-width:600px){
  .mini-header{ background:#fff; border-bottom:1px solid #fff; }
}

@media (max-width: 959px){
  .mini-header{
    position: fixed; /* force always visible */
    top: 0; left: 0; right: 0;
  }
  /* push page content down so it doesn't hide under the fixed header */
  body{ padding-top: var(--mh-h); }
.mh-container{
   margin:0 auto; 
  }
}

/* ===== Modal styles unchanged (kept as-is) ===== */
/* Header always on top */
.mini-header{
  position: fixed; top:0; left:0; right:0;
  z-index: 99999 !important;
  overflow: visible !important;
}


               
/* --- Header: fixed, centered, above all --- */
.mini-header{
  position: fixed; top:0; left:0; right:0;
  z-index: 99999 !important;
  background:#fff; border-bottom:1px solid var(--border);
  overflow: visible;
}
.mh-container{
  max-width: 1200px;
  margin: 0 auto;              /* center on desktop */
  height: 90px;
  display:flex; align-items:center; justify-content:space-between;
  padding: 0 16px;
}

/* Dropdown as topmost popover */
.mh-help{ position:relative; overflow:visible; }

.mh-help.open .mh-menu{ display:block; }

  /* ===== RESET bad fixed menu & anchor to button ===== */
.mh-help{ position: relative !important; overflow: visible !important; }

.mini-header .mh-help .mh-menu{
  position: absolute !important;   /* back to hanging from the button */
  top: calc(100% + 8px) !important;
  right: 0 !important;
  left: auto !important;
  transform: none !important;
  z-index: 10000 !important;
}

/* Center header container on wide screens */
.mh-container{
  max-width: 1200px;
  margin: 0 auto !important;
  padding: 0 16px;
}

/* Desktop/tablet: header sticky, not fixed */
@media (min-width: 960px){
  .mini-header{ position: sticky !important; top: 0; }
}

</style>

<header class="mini-header" role="banner">
  <div class="mh-container">
    <!-- Logo -->
    <a href="/" class="mh-logo" aria-label="Paragon AFS Home">
      <img src="../assets/img/paragon_logo.png" alt="Paragon AFS">
    </a>

    <!-- Help icon + dropdown menu -->
    <div class="mh-help">
      <button class="mh-help-btn" aria-label="Help and account" aria-haspopup="menu" aria-expanded="false">
        <!-- Blue info icon -->
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="11.5" x2="12" y2="16"></line>
          <circle cx="12" cy="8" r="1.2"></circle>
        </svg>
        <span>Help</span>
      </button>

      <div id="mhHelpMenu" class="mh-menu" role="menu" aria-label="Help and account menu">
        <a href="/help" role="menuitem">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line>
          </svg>
          Help Center
        </a>
       <a href="#" class="js-contact-open" role="menuitem">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V5a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
  </svg>
  Contact Us
</a>
        <hr>
        <?php if (!isset($_SESSION['email'])): ?>
          <a href="../auth/" role="menuitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Sign In
          </a>
        <?php else: ?>
          <a href="../auth/logout.php" role="menuitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Sign Out
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

           
</header>


<!-- Contact Us Modal -->
<div id="contactModal" class="modal" aria-hidden="true">
  <div class="modal__backdrop" data-close></div>

  <div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="contactTitle">
    <div class="modal__header">
      <h2 class="modal__title" id="contactTitle">Contact Us</h2>
      <button class="modal__close" aria-label="Close" data-close>&times;</button>
    </div>

    <div class="modal__grid">
      <div class="contact-card">
        <div class="icon-circle">
          <!-- mobile phone -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
        </div>
        <div class="contact-body">
          <div class="label">Mobile</div>
          <a href="tel:647-909-8484">647-909-8484</a>
          <a href="tel:437-881-9175">437-881-9175</a>
        </div>
      </div>

      <div class="contact-card">
        <div class="icon-circle">
          <!-- office phone -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 11 19a19.8 19.8 0 0 1-8.82 2.92A2 2 0 0 1 0 19.92v-3a2 2 0 0 1 1.72-2 12.9 12.9 0 0 0 5.4-2.2l1.2-1.06a2 2 0 0 1 2.24-.27l2.8 1.4a12.93 12.93 0 0 0 5.64 1.4 2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="contact-body">
          <div class="label">Office Number</div>
          <a href="tel:416-477-3359">416-477-3359</a>
        </div>
      </div>

      <div class="contact-card">
        <div class="icon-circle">
          <!-- mail -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
        </div>
        <div class="contact-body">
          <div class="label">Email</div>
          <a href="mailto:info@paragonafs.ca">info@paragonafs.ca</a>
        </div>
      </div>

      <div class="contact-card">
        <div class="icon-circle">
          <!-- map pin -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="contact-body">
          <div class="label">Address</div>
          <a target="_blank" rel="noopener" href="https://www.google.com/maps?ll=43.66714,-79.733547&z=16&t=m&hl=en&gl=PH&mapclient=embed&q=1+Bartley+Bull+Pkwy+%2319a+Brampton,+ON+L6W+3T7+Canada">
            #19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7
          </a>
        </div>
      </div>

      <div class="contact-docs">
        <div class="label">Documents List</div>
        <div class="docs-slot" id="contact-docs-slot">Add your documents list here…</div>
      </div>
    </div>
  </div>
</div>

<script>
  // Keep --mh-h equal to the actual header height (handles logo/load/resizes)
  (function(){
    const header = document.querySelector('.mini-header');
    if(!header) return;
    function setH(){
      const h = header.offsetHeight || 90;
      document.documentElement.style.setProperty('--mh-h', h + 'px');
    }
    setH();
    new ResizeObserver(setH).observe(header);
    window.addEventListener('load', setH);
    window.addEventListener('resize', setH);
  })();
</script>

<script>
  (function(){
    const help = document.querySelector('.mh-help');
    if(!help) return;
    const btn = help.querySelector('.mh-help-btn');

    function open(){ help.classList.add('open'); btn.setAttribute('aria-expanded','true'); }
    function close(){ help.classList.remove('open'); btn.setAttribute('aria-expanded','false'); }

    btn.addEventListener('click', () => help.classList.contains('open') ? close() : open());
    document.addEventListener('click', (e)=>{ if(!help.contains(e.target)) close(); });
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
  })();
</script>


<script>
(function(){
  const modal = document.getElementById('contactModal');
  const openers = document.querySelectorAll('.js-contact-open'); // any trigger
  const closeEls = modal.querySelectorAll('[data-close]');
  let lastFocus = null;

  function openModal(){
    lastFocus = document.activeElement;
    modal.classList.add('is-open');
    modal.removeAttribute('aria-hidden');
    document.body.style.overflow = 'hidden';
    modal.querySelector('.modal__close').focus();
    // also close the help dropdown if open
    document.querySelector('.mh-help')?.classList.remove('open');
  }
  function closeModal(){
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
    if(lastFocus) lastFocus.focus();
  }

  openers.forEach(el => el.addEventListener('click', e => { e.preventDefault(); openModal(); }));
  closeEls.forEach(el => el.addEventListener('click', closeModal));
  modal.addEventListener('click', e => { if(e.target.matches('.modal__backdrop,[data-close]')) closeModal(); });
  document.addEventListener('keydown', e => { if(e.key === 'Escape' && modal.classList.contains('is-open')) closeModal(); });

  // Simple focus trap
  modal.addEventListener('keydown', e=>{
    if(e.key !== 'Tab' || !modal.classList.contains('is-open')) return;
    const focusables = modal.querySelectorAll('a,button,[tabindex]:not([tabindex="-1"])');
    if(!focusables.length) return;
    const first = focusables[0], last = focusables[focusables.length-1];
    if(e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
    if(!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
  });
})();
</script>

