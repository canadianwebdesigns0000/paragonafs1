<script>
(function () {
  function el(q, r = document) { return r.querySelector(q); }
  function all(q, r = document) { return Array.from(r.querySelectorAll(q)); }
  function isShown(node) {
    if (!node) return false;
    const s = getComputedStyle(node);
    return s.display !== 'none' && s.visibility !== 'hidden';
  }

  function ensureBanner() {
    const wrap = el('#welcome-panel .qs-wrap') || el('#welcome-panel');
    let b = el('#qsError');
    if (!b) {
      b = document.createElement('div');
      b.id = 'qsError';
      b.className = 'qs-error-banner';
      b.setAttribute('role', 'alert');
      b.setAttribute('aria-live', 'polite');
      b.innerHTML = '<h3>A selection is required.</h3><p>To proceed, please fill in or correct the required field(s).</p>';
      wrap?.insertBefore(b, wrap.firstChild);
    }
    return b;
  }

  function clearErrors() {
    el('#qsError')?.classList.remove('show');
    all('#welcome-panel .is-invalid').forEach(n => n.classList.remove('is-invalid'));
    all('#welcome-panel .fi-error-text').forEach(n => n.remove());
  }

  // clear error on a specific host
  function clearErrorOn(host) {
    if (!host) return;
    host.classList.remove('is-invalid');
    const e = host.querySelector('.fi-error-text');
    if (e) e.remove();
  }

  function addError(host, msg) {
    if (!host) host = el('#welcome-panel');
    host.classList.add('is-invalid');
    if (host.querySelector(':scope > .fi-error-text')) return host;
    const m = document.createElement('div');
    m.className = 'fi-error-text';
    m.textContent = msg || 'This field is required.';
    host.appendChild(m);
    return host;
  }

  function radioVal(name) {
    const r = el(`input[name="${name}"]:checked`);
    return r ? r.value : '';
  }
  function clearRadioGroup(name) {
    all(`input[name="${name}"]`).forEach(r => r.checked = false);
  }
  function setRadioValue(name, val) {
    if (!val) { clearRadioGroup(name); return; }
    const r = el(`input[name="${name}"][value="${val}"]`);
    if (r) r.checked = true;
  }
  function selectValue(sel) {
    if (!sel) return '';
    const idx = sel.selectedIndex;
    if (idx < 0) return '';
    const opt = sel.options[idx];
    if (!opt || opt.disabled || opt.value === '') return '';
    return opt.value || opt.text || '';
  }
  function blockHasCheckedRadios(block) {
    if (!block) return false;
    return !!block.querySelector('input[type="radio"]:checked, input[type="checkbox"]:checked');
  }

  // ===== PER-STATUS MEMORY =====
  const statusMemory = {};
  const dependentFields = ['spouse_in_canada', 'spouseFile', 'children'];
  let lastStatus = '';

  function getCurrentStatus() {
    const sel = el('#marital_status_select');
    const msSel = selectValue(sel);
    const msRad = radioVal('marital_status');
    return msRad || msSel || '';
  }

  function saveCurrentStatusAnswers() {
    if (!lastStatus) return;
    const store = {};
    dependentFields.forEach(name => {
      store[name] = radioVal(name); // '' if none
    });
    statusMemory[lastStatus] = store;
  }

  function restoreStatusAnswers(newStatus) {
    const saved = statusMemory[newStatus];
    dependentFields.forEach(name => {
      if (saved && saved[name]) {
        setRadioValue(name, saved[name]);
      } else {
        clearRadioGroup(name);
      }
    });
  }

  // show/hide children (and clear errors when hidden)
  function updateChildrenVisibility(status) {
    const outer = el('#children-block');
    const inner = outer ? outer.querySelector('.yn-group') : null;
    if (!outer) return;

    if (status && status !== 'Single') {
      outer.style.display = '';
    } else {
      outer.style.display = 'none';
      // clear errors on both wrappers
      clearErrorOn(outer);
      if (inner) clearErrorOn(inner);
    }
  }

  // clear errors on dependent groups when status changes
  function clearDependentErrors() {
    // spouse in Canada
    const spouseInner = el('.yn-group input[name="spouse_in_canada"]')?.closest('.yn-group');
    // spouse file
    const spouseFileInner = el('#spouse-file-block .yn-group');
    // children
    const childrenOuter = el('#children-block');
    const childrenInner = childrenOuter ? childrenOuter.querySelector('.yn-group') : null;

    clearErrorOn(spouseInner);
    clearErrorOn(spouseFileInner);
    clearErrorOn(childrenOuter);
    clearErrorOn(childrenInner);

    // hide banner if nothing else invalid
    const panel = el('#welcome-panel');
    if (panel && !panel.querySelector('.is-invalid')) {
      el('#qsError')?.classList.remove('show');
    }
  }

  function onMaritalStatusChange(newStatus) {
    // save previous status answers
    saveCurrentStatusAnswers();
    // switch status
    lastStatus = newStatus;
    // restore answers for this status
    restoreStatusAnswers(newStatus);
    // show/hide blocks
    updateChildrenVisibility(newStatus);
    // clear old errors from previous status
    clearDependentErrors();
  }

  // ===== VALIDATION =====
  function validateWelcome() {
    clearErrors(); // start clean
    ensureBanner();
    const errs = [];

    const selMS = el('#marital_status_select');
    const msSel = selectValue(selMS);
    const msRad = radioVal('marital_status');
    const ms = msRad || msSel;

    if (!ms) {
      errs.push(addError(selMS?.closest('.qs-block') || selMS || el('.qs-choicegrid'), 'Please select your marital status.'));
    } else if (msSel) {
      // keep radios in sync
      const r = el(`input[name="marital_status"][value="${msSel}"]`);
      if (r && !r.checked) r.checked = true;
    }

    const marriedLike = (ms === 'Married' || ms === 'Common Law');
    const needsSDW = (ms === 'Separated' || ms === 'Divorced' || ms === 'Widowed');
    const singleLike = (ms === 'Single');

    // marriage/common-law date
    const marryBlock = el('#status-date-block');
    if (marriedLike && marryBlock) {
      const d = el('#status_date');
      if (!d || !d.value.trim()) {
        errs.push(addError(marryBlock, ms === 'Common Law'
          ? 'Please enter your common-law status date.'
          : 'Please enter your date of marriage.'
        ));
      }
    }

    // separated/divorced/widowed date
    const sdwBlock = el('#status-date-sdw-block');
    if (needsSDW && sdwBlock) {
      const sdw = el('#status_date_sdw');
      const lbl = el('#status-date-sdw-label')?.textContent || 'date';
      if (!sdw || !sdw.value.trim()) {
        errs.push(addError(sdwBlock, `Please enter ${lbl.toLowerCase()}.`));
      }
    }

    // spouse in Canada
    if (marriedLike) {
      const spouseYN = el('.yn-group input[name="spouse_in_canada"]')?.closest('.yn-group');
      if (spouseYN && !blockHasCheckedRadios(spouseYN)) {
        errs.push(addError(spouseYN, 'Please choose Yes or No.'));
      }
    }

    // spouse wants to file
    const inCanadaYes = el('#spouse_in_canada_yes')?.checked;
    const spouseFileYN = el('#spouse-file-block .yn-group');
    if (marriedLike && inCanadaYes && spouseFileYN && isShown(spouseFileYN)) {
      if (!blockHasCheckedRadios(spouseFileYN)) {
        errs.push(addError(spouseFileYN, 'Please indicate if your spouse will file.'));
      }
    }

    // children – validate for ALL except single
    const childrenOuter = el('#children-block');
    const childrenInner = childrenOuter ? childrenOuter.querySelector('.yn-group') : null;
    const childrenHost = childrenInner || childrenOuter;
    if (!singleLike && childrenHost && isShown(childrenHost)) {
      if (!blockHasCheckedRadios(childrenHost)) {
        errs.push(addError(childrenHost, 'Please select if you have children.'));
      }
    }

    if (errs.length) {
      el('#qsError')?.classList.add('show');
      const first = errs[0];
      if (first) {
        const focusable = first.querySelector('input, select, button, textarea');
        if (focusable?.focus) focusable.focus();
        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return false;
    }
    return true;
  }

  // live clear
  function attachLiveCleanup() {
    const panel = el('#welcome-panel');
    if (!panel) return;
    function maybeClear(e) {
      const host = e.target.closest('.is-invalid');
      if (!host) return;
      host.classList.remove('is-invalid');
      host.querySelector('.fi-error-text')?.remove();
      if (!panel.querySelector('.is-invalid')) el('#qsError')?.classList.remove('show');
    }
    panel.addEventListener('input', maybeClear, true);
    panel.addEventListener('change', maybeClear, true);
  }

  function wireButton() {
    const btn = el('#welcome-panel #qs-continue');
    if (!btn) return;

    // capture = true so this runs BEFORE the main app's listener
    btn.addEventListener('click', function (ev) {
      // keep UI in sync if your main app exposes this
      if (window.App?.refreshWelcomeBlocks) {
        window.App.refreshWelcomeBlocks();
      }

      const ok = validateWelcome();
      if (!ok) {
        // block navigation ONLY when invalid
        ev.preventDefault();
        ev.stopPropagation();
        ev.stopImmediatePropagation(); // ← this stops the other click handler
        return;
      }
      // if valid: do nothing — let the main script handle the panel switch
    }, true);
  }

  function wireMaritalStatus() {
    const sel = el('#marital_status_select');
    if (sel) {
      sel.addEventListener('change', function () {
        onMaritalStatusChange(this.value);
      });
    }
    all('input[name="marital_status"]').forEach(r => {
      r.addEventListener('change', function () {
        const s = el('#marital_status_select');
        if (s) s.value = this.value;
        onMaritalStatusChange(this.value);
      });
    });

    // init
    const initStatus = getCurrentStatus();
    if (initStatus) {
      lastStatus = initStatus;
      updateChildrenVisibility(initStatus);
      if (!statusMemory[initStatus]) statusMemory[initStatus] = {};
    }
  }

  ensureBanner();
  attachLiveCleanup();
  wireButton();
  wireMaritalStatus();
})();
</script>





<!-- PERSONAL ERROR 2 SCRIPT -->

<script>
(function(){
  // ---------- tiny helpers ----------
  const $  = (q, r=document)=>r.querySelector(q);
  const $$ = (q, r=document)=>Array.from(r.querySelectorAll(q));

  const panel = document.querySelector('.pi-main[data-panel="personal"]');
  if (!panel) return;

  // ---------- banner ----------
  function ensureBanner(){
    let b = panel.querySelector('#personalError');
    if (!b){
      b = document.createElement('div');
      b.id = 'personalError';
      b.className = 'qs-error-banner';
      b.setAttribute('role','alert');
      b.setAttribute('aria-live','polite');
      b.innerHTML = `
        <h3>A selection is required.</h3>
        <p>To proceed, please fill in or correct the required field(s).</p>
      `;
      panel.insertBefore(b, panel.firstElementChild);
    }
    return b;
  }
  function showBanner(){ ensureBanner().classList.add('show'); }
  function hideBanner(){ panel.querySelector('#personalError')?.classList.remove('show'); }

  // ---------- error helpers ----------
  function clearErrors(){
    hideBanner();
    $$('.is-invalid', panel).forEach(n=>n.classList.remove('is-invalid'));
    $$('.fi-error-text', panel).forEach(n=>n.remove());
  }

  // put error under the right container
  function errorUnder(anchor, msg){
    if (!anchor) return null;

    // special case: DOB -> we want the error on the fi-grid so calendar icon doesn't drop
    if (anchor.id === 'dob') {
      const grid = anchor.closest('.fi-grid') || anchor.closest('.fi-group') || anchor.parentElement;
      if (!grid) return null;
      grid.classList.add('is-invalid');
      if (!grid.querySelector(':scope > .fi-error-text')) {
        const m = document.createElement('div');
        m.className = 'fi-error-text';
        m.textContent = msg || 'This field is required.';
        grid.appendChild(m);
      }
      return grid;
    }

    const host = anchor.closest('.fi-group, .yn-group, .qs-block') || anchor.parentElement || anchor;
    host.classList.add('is-invalid');

    if (!host.querySelector(':scope > .fi-error-text')) {
      const m = document.createElement('div');
      m.className = 'fi-error-text';
      m.textContent = msg || 'This field is required.';
      host.appendChild(m);
    }
    return host;
  }

  // ---------- value helpers ----------
  const get = id => panel.querySelector('#' + id);
  const val = id => (get(id)?.value || '').trim();

  // ---------- validators ----------
  const twoLetters = v => /^[A-Za-z][A-Za-z\-' ]+$/.test(v) && v.replace(/[^A-Za-z]/g,'').length >= 2;

  const MONTHS = {
    jan:1, january:1,
    feb:2, february:2,
    mar:3, march:3,
    apr:4, april:4,
    may:5,
    jun:6, june:6,
    jul:7, july:7,
    aug:8, august:8,
    sep:9, sept:9, september:9,
    oct:10, october:10,
    nov:11, november:11,
    dec:12, december:12
  };

  function isValidDate(y, m, d){
    const dt = new Date(y, m-1, d);
    return dt.getFullYear() === y && dt.getMonth() === m-1 && dt.getDate() === d;
  }

  // accepts: 05 | Jan | 2016, 5 Jan 2016, 05-Jan-2016, 05/Jan/2016
  function parseDobDisplay(v){
    if (!v) return null;
    // split on any non-word-ish separator
    const parts = v.trim().split(/[\s|\/\-]+/).filter(Boolean);
    if (parts.length !== 3) return null;

    const d = parseInt(parts[0], 10);
    const mStr = parts[1].toLowerCase();
    const y = parseInt(parts[2], 10);

    const m = MONTHS[mStr];
    if (!m || isNaN(d) || isNaN(y)) return null;
    if (!isValidDate(y, m, d)) return null;

    return { d, m, y };
  }

  const sinOk    = v => /^\d{9}$/.test(v.replace(/\D/g,''));
  const postalOk = v => /^[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d$/.test(v.trim());
  const phoneOk  = v => v.replace(/\D/g,'').length >= 10;
  const emailOk  = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

  // ---------- main validate ----------
  function validatePersonal(){
    clearErrors();
    const errs = [];

    // names
    if (!twoLetters(val('first_name')))
      errs.push(errorUnder(get('first_name'), 'Your first name needs at least two letters.'));
    if (!twoLetters(val('last_name')))
      errs.push(errorUnder(get('last_name'), 'Your last name needs at least two letters.'));

    // DOB (DD | MMM | YYYY)
    const dobInput = get('dob');
    const dobParsed = parseDobDisplay(val('dob'));
    if (!dobParsed){
      errs.push(errorUnder(dobInput, 'Enter date as DD | MMM | YYYY.'));
    } else {
      // normalize to your display style
      const day = String(dobParsed.d).padStart(2,'0');
      // find 3-letter month
      const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const monthLabel = monthNames[dobParsed.m - 1];
      dobInput.value = `${day} | ${monthLabel} | ${dobParsed.y}`;
    }

    // SIN
    const sinRaw = val('sin').replace(/\D/g,'');
    if (!sinOk(sinRaw))
      errs.push(errorUnder(get('sin'), 'Enter a 9-digit SIN, numbers only.'));

    // gender
    if (!panel.querySelector('input[name="gender"]:checked')){
      const anyGenderInput = panel.querySelector('input[name="gender"]');
      errs.push(errorUnder(anyGenderInput, 'Please select a gender.'));
    }

    // address
    if (!val('street'))   errs.push(errorUnder(get('street'),   'Street is required.'));
    if (!val('city'))     errs.push(errorUnder(get('city'),     'City is required.'));
    if (!val('province')) errs.push(errorUnder(get('province'), 'State/Province is required.'));
    if (!postalOk(val('postal'))) errs.push(errorUnder(get('postal'), 'Enter a valid postal code (e.g., A1A 1A1).'));
    if (!val('country'))  errs.push(errorUnder(get('country'),  'Country is required.'));

    // contact
    if (!phoneOk(val('phone'))) errs.push(errorUnder(get('phone'), 'Enter a valid phone number (10+ digits).'));
    if (!emailOk(val('email'))) errs.push(errorUnder(get('email'), 'Enter a valid email address.'));

    if (errs.length){
      showBanner();
      const first = errs[0];
      if (first){
        const focusable = first.querySelector('input, select, textarea, button') || first;
        focusable?.focus?.();
        first.scrollIntoView({ behavior:'smooth', block:'center' });
      }
      return false;
    }
    return true;
  }

// ------- Live cleanup when fixing fields -------
panel.addEventListener('input', (e)=>{
  // special case: DOB error sits on the grid
  if (e.target.id === 'dob') {
    const dobGrid = panel.querySelector('.fi-grid.is-invalid:has(#dob)');
    if (dobGrid) {
      dobGrid.classList.remove('is-invalid');
      dobGrid.querySelector('.fi-error-text')?.remove();
      if (!panel.querySelector('.is-invalid')) hideBanner();
    }
    return;
  }

  const host = e.target.closest('.fi-group, .yn-group, .qs-block, .fi-grid');
  if (host?.classList.contains('is-invalid')){
    host.classList.remove('is-invalid');
    host.querySelector('.fi-error-text')?.remove();
    if (!panel.querySelector('.is-invalid')) hideBanner();
  }
}, true);

panel.addEventListener('change', (e)=>{
  if (e.target.id === 'dob') {
    const dobGrid = panel.querySelector('.fi-grid.is-invalid:has(#dob)');
    if (dobGrid) {
      dobGrid.classList.remove('is-invalid');
      dobGrid.querySelector('.fi-error-text')?.remove();
      if (!panel.querySelector('.is-invalid')) hideBanner();
    }
    return;
  }

  const host = e.target.closest('.fi-group, .yn-group, .qs-block, .fi-grid');
  if (host?.classList.contains('is-invalid')){
    host.classList.remove('is-invalid');
    host.querySelector('.fi-error-text')?.remove();
    if (!panel.querySelector('.is-invalid')) hideBanner();
  }
}, true);

  // ---------- hook Continue ----------
  const nextBtn = panel.querySelector('.continue-btn[data-goto="next"]');
  if (nextBtn){
    nextBtn.addEventListener('click', (ev)=>{
      if (!validatePersonal()){
        ev.preventDefault();
        ev.stopPropagation();
        ev.stopImmediatePropagation();
      }
    }, true);
  }

  // create banner hidden
  ensureBanner();
})();


</script>


<!-- TAX ERROR 3 SCRIPT -->
<script>
(function () {
  const panel = document.querySelector('.pi-main[data-panel="tax"]');
  if (!panel) return;

  const $  = (q, r=document) => r.querySelector(q);
  const $$ = (q, r=document) => Array.from(r.querySelectorAll(q));

  /* ----------------------------------------------------------
     ERROR BANNER
     ---------------------------------------------------------- */
  function ensureBanner() {
    let b = panel.querySelector('#taxError');
    if (!b) {
      b = document.createElement('div');
      b.id = 'taxError';
      b.className = 'qs-error-banner';
      b.setAttribute('role','alert');
      b.setAttribute('aria-live','polite');
      b.innerHTML = `
        <h3>A selection is required.</h3>
        <p>To proceed, please fill in or correct the required field(s).</p>
      `;
      panel.insertBefore(b, panel.firstElementChild);
    }
    return b;
  }
  function showBanner(){ ensureBanner().classList.add('show'); }
  function hideBanner(){ panel.querySelector('#taxError')?.classList.remove('show'); }

  /* ----------------------------------------------------------
     DATE HELPERS
     ---------------------------------------------------------- */
  const MONTHS = {
    jan:1,january:1,
    feb:2,february:2,
    mar:3,march:3,
    apr:4,april:4,
    may:5,
    jun:6,june:6,
    jul:7,july:7,
    aug:8,august:8,
    sep:9,sept:9,september:9,
    oct:10,october:10,
    nov:11,november:11,
    dec:12,december:12
  };
  function isValidDate(y,m,d){
    const dt = new Date(y,m-1,d);
    return dt.getFullYear()===y && dt.getMonth()===m-1 && dt.getDate()===d;
  }
  function parseDobDisplay(v){
    if (!v) return null;
    const parts = v.trim().split(/[\s|\/\-]+/).filter(Boolean);
    if (parts.length !== 3) return null;
    const d = parseInt(parts[0],10);
    const mStr = parts[1].toLowerCase();
    const y = parseInt(parts[2],10);
    const m = MONTHS[mStr];
    if (!m || isNaN(d) || isNaN(y)) return null;
    if (!isValidDate(y,m,d)) return null;
    return {d,m,y};
  }
  function normalizeDateInput(inputEl){
    const parsed = parseDobDisplay(inputEl.value);
    if (!parsed) return false;
    const day = String(parsed.d).padStart(2,'0');
    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const monthLabel = monthNames[parsed.m - 1];
    inputEl.value = `${day} | ${monthLabel} | ${parsed.y}`;
    const bindSel = inputEl.getAttribute('data-bind');
    if (bindSel) {
      const hidden = panel.querySelector(bindSel);
      if (hidden) hidden.value = `${parsed.y}-${String(parsed.m).padStart(2,'0')}-${day}`;
    }
    return true;
  }

  /* ----------------------------------------------------------
     CLEAR ERRORS
     ---------------------------------------------------------- */
  function clearErrors() {
    hideBanner();
    $$('.is-invalid', panel).forEach(n => n.classList.remove('is-invalid'));
    $$('.fi-error-text', panel).forEach(n => n.remove());
  }

  /* ----------------------------------------------------------
     ERROR PLACEMENT
     ---------------------------------------------------------- */
  function errorUnder(anchor, msg) {
    if (!anchor) return null;

    // DATE inputs: absolute so layout doesn't shift
    if (
      anchor.classList.contains('dob-input') ||
      anchor.id === 'entry_date_display' ||
      anchor.id === 'moved_date_display' ||
      anchor.id === 'first_home_purchase_display'
    ) {
      const group = anchor.closest('.fi-group');
      if (group) {
        group.classList.add('is-invalid', 'has-date-error');
        if (!group.querySelector('.fi-error-text')) {
          const m = document.createElement('div');
          m.className = 'fi-error-text fi-error-abs';
          m.textContent = msg || 'Enter date as DD | MMM | YYYY.';
          group.appendChild(m);
        }
        return group;
      }
    }

    // RENT block: whole block
    if (anchor.id === 'rent-addresses' || (anchor.closest && anchor.closest('#rent-addresses'))) {
      const wrap = anchor.id === 'rent-addresses' ? anchor : anchor.closest('#rent-addresses');
      wrap.classList.add('is-invalid');
      if (!wrap.querySelector(':scope > .fi-error-text')) {
        const m = document.createElement('div');
        m.className = 'fi-error-text';
        m.textContent = msg || 'Please add at least one rent address.';
        wrap.appendChild(m);
      }
      return wrap;
    }

    // radio yes/no
    if (anchor.type === 'radio') {
      const ynGroup = anchor.closest('.yn-group');
      if (ynGroup) {
        ynGroup.classList.add('is-invalid');
        if (!ynGroup.querySelector('.fi-error-text')) {
          const m = document.createElement('div');
          m.className = 'fi-error-text';
          m.textContent = msg || 'Please select Yes or No.';
          ynGroup.appendChild(m);
        }
        return ynGroup;
      }
    }

    // normal fields
    const group = anchor.closest('.fi-group');
    if (group) {
      group.classList.add('is-invalid');
      if (!group.querySelector('.fi-error-text')) {
        const m = document.createElement('div');
        m.className = 'fi-error-text';
        m.textContent = msg || 'This field is required.';
        group.appendChild(m);
      }
      return group;
    }

    // fallback
    const host = anchor.closest('.qs-block') || anchor.parentElement;
    host.classList.add('is-invalid');
    if (!host.querySelector(':scope > .fi-error-text')) {
      const m = document.createElement('div');
      m.className = 'fi-error-text';
      m.textContent = msg || 'This field is required.';
      host.appendChild(m);
    }
    return host;
  }

  /* ----------------------------------------------------------
     PANEL PARTS WE TOGGLE
     ---------------------------------------------------------- */
  const priorSection     = $('#prior-customer-section', panel);
  const firsttimeDetails = $('#firsttime-details', panel);
  const wiWrapper        = $('#wi-wrapper', panel);
  const movedSection     = $('#moved-section', panel);
  const movexpDetails    = $('#movexp-details', panel);
  const fthbDetails      = $('#fthb-details', panel);
  const ownersGrid       = $('#owners-grid', panel);
  const ownersWrap       = $('#owners-wrap', panel);
  const rentBlock        = $('#rent-block', panel);
  const claimRentBlock   = $('#claim-rent-block', panel);
  const rentAddresses    = $('#rent-addresses', panel);

  function toggleFirstTime(){
    const sel = panel.querySelector('input[name="first_time"]:checked');
    if (!sel) {
      priorSection?.classList.add('is-hidden');
      firsttimeDetails?.classList.add('is-hidden');
      wiWrapper?.classList.add('is-hidden');
      return;
    }
    if (sel.value === 'no') {
      priorSection?.classList.remove('is-hidden');
      firsttimeDetails?.classList.add('is-hidden');
      wiWrapper?.classList.add('is-hidden');
    } else {
      priorSection?.classList.add('is-hidden');
      firsttimeDetails?.classList.remove('is-hidden');
      const entry = $('#entry_date_display', panel);
      if (entry && entry.value.trim() !== '') {
        wiWrapper?.classList.remove('is-hidden');
      }
    }
  }
  function toggleMoved(){
    const sel = panel.querySelector('input[name="moved_province"]:checked');
    if (sel && sel.value === 'yes') movedSection?.classList.remove('is-hidden');
    else movedSection?.classList.add('is-hidden');
  }
  function toggleMovExp(){
    const sel = panel.querySelector('input[name="moving_expenses_claim"]:checked');
    if (sel && sel.value === 'yes') movexpDetails?.classList.remove('is-hidden');
    else movexpDetails?.classList.add('is-hidden');
  }
  function toggleFTHB(){
    const sel = panel.querySelector('input[name="first_home_buyer"]:checked');
    if (sel && sel.value === 'yes') fthbDetails?.classList.remove('is-hidden');
    else fthbDetails?.classList.add('is-hidden');
  }
function toggleClaimFull(){
  const sel = panel.querySelector('input[name="claim_full"]:checked');
  const wrap = $('#owners-wrap', panel);     // inner div with the input
  const rentBlock = $('#rent-block', panel); // "Are you living on Rent?"

  if (sel && sel.value === 'no') {
    // show extra field
    wrap?.classList.remove('is-hidden');
    wrap?.setAttribute('aria-hidden','false');

    // keep the outer grid visible (do NOT hide #owners-grid)
    // add the gap before rent
    rentBlock?.classList.add('rent-gap');
  } else {
    // hide only the inner field
    wrap?.classList.add('is-hidden');
    wrap?.setAttribute('aria-hidden','true');

    // remove gap before rent
    rentBlock?.classList.remove('rent-gap');
  }
}

  function toggleRent(){
    const onRent = panel.querySelector('input[name="onRent"]:checked');
    if (onRent && onRent.value === 'yes') {
      claimRentBlock.style.display = '';
      const claim = panel.querySelector('input[name="claimRent"]:checked');
      if (claim && claim.value === 'yes') {
        rentAddresses.style.display = '';
      } else {
        rentAddresses.style.display = 'none';
      }
    } else {
      claimRentBlock.style.display = 'none';
      rentAddresses.style.display = 'none';
    }
  }

  /* ----------------------------------------------------------
     VALIDATION
     ---------------------------------------------------------- */
  function validateTaxPanel() {
    clearErrors();
    const errs = [];

    // 1) first time?
    const firstSel = panel.querySelector('input[name="first_time"]:checked');
    if (!firstSel) {
      errs.push(errorUnder($('#first_yes', panel), 'Please select Yes or No.'));
    } else if (firstSel.value === 'no') {
      if (!panel.querySelector('input[name="paragon_prior"]:checked')) {
        errs.push(errorUnder($('#paragon_yes', panel), 'Please select Yes or No.'));
      }
      const years = $('#return_years', panel);
      if (years && !years.value.trim()) {
        errs.push(errorUnder(years, 'Please enter the year(s) you want to file.'));
      }
    } else if (firstSel.value === 'yes') {
      const entryDisp = $('#entry_date_display', panel);
      if (!parseDobDisplay(entryDisp.value)) {
        errs.push(errorUnder(entryDisp, 'Enter date as DD | MMM | YYYY.'));
      } else {
        normalizeDateInput(entryDisp);
      }
      const birthCountry = $('#birth_country', panel);
      if (!birthCountry.value.trim()) {
        errs.push(errorUnder(birthCountry, 'Country is required.'));
      }
   if (wiWrapper && wiWrapper.classList.contains('is-hidden') === false) {
  const incomeRows = panel.querySelectorAll('.wi-col--income .wi-row');
  const periodRows = panel.querySelectorAll('.wi-col--period .wi-row');

  ['inc_y1','inc_y2','inc_y3'].forEach((id, idx) => {
    const inp = $('#'+id, panel);
    if (inp && !inp.value.trim()) {
      // normal error on the input
      errs.push(errorUnder(inp, 'Enter amount or 0.'));

      // right column row
      const incomeRow = inp.closest('.wi-row');
      if (incomeRow) incomeRow.classList.add('wi-row-error');

      // left column row with the same index
      const periodRow = periodRows[idx];
      if (periodRow) periodRow.classList.add('wi-row-error');
    }
  });
}


    }

    // 2) moved?
    const movedSel = panel.querySelector('input[name="moved_province"]:checked');
    if (!movedSel) {
      errs.push(errorUnder($('#mprov_yes', panel), 'Please select Yes or No.'));
    } else if (movedSel.value === 'yes') {
      const movedDisp = $('#moved_date_display', panel);
      if (!parseDobDisplay(movedDisp.value)) {
        errs.push(errorUnder(movedDisp, 'Enter date as DD | MMM | YYYY.'));
      } else {
        normalizeDateInput(movedDisp);
      }
      const provFrom = $('#prov_from', panel);
      const provTo   = $('#prov_to', panel);
      if (!provFrom.value.trim()) errs.push(errorUnder(provFrom, 'Select a province.'));
      if (!provTo.value.trim())   errs.push(errorUnder(provTo,   'Select a province.'));

      const movexpSel = panel.querySelector('input[name="moving_expenses_claim"]:checked');
      if (!movexpSel) {
        errs.push(errorUnder($('#movexp_yes', panel), 'Please select Yes or No.'));
      } else if (movexpSel.value === 'yes') {
        const prevAddr = $('#moving_prev_address', panel);
        const dist     = $('#moving_distance', panel);
        if (!prevAddr.value.trim()) errs.push(errorUnder(prevAddr, 'Previous address is required.'));
        if (!dist.value.trim())     errs.push(errorUnder(dist, 'Distance is required.'));
      }
    }

    // 3) FTHB
    const fthbSel = panel.querySelector('input[name="first_home_buyer"]:checked');
    if (!fthbSel) {
      errs.push(errorUnder($('#fthb_yes', panel), 'Please select Yes or No.'));
    } else if (fthbSel.value === 'yes') {
      const purchase = $('#first_home_purchase_display', panel);
      if (!parseDobDisplay(purchase.value)) {
        errs.push(errorUnder(purchase, 'Enter date as DD | MMM | YYYY.'));
      } else {
        normalizeDateInput(purchase);
      }
    }

    // 4) sole owner
    const claimSel = panel.querySelector('input[name="claim_full"]:checked');
    if (!claimSel) {
      errs.push(errorUnder($('#claim_full_yes', panel), 'Please select Yes or No.'));
    } else if (claimSel.value === 'no') {
      const ownerCount = $('#owner_count', panel);
      if (!ownerCount.value.trim()) {
        errs.push(errorUnder(ownerCount, '# of owners is required.'));
      }
    }

    // 5) rent
    const rentSel = panel.querySelector('input[name="onRent"]:checked');
    if (!rentSel) {
      errs.push(errorUnder($('#onrent_yes', panel), 'Please choose Yes or No.'));
    } else if (rentSel.value === 'yes') {
      const claimRentSel = panel.querySelector('input[name="claimRent"]:checked');
      if (!claimRentSel) {
        errs.push(errorUnder($('#claimrent_yes', panel), 'Please choose Yes or No.'));
      } else if (claimRentSel.value === 'yes') {
        // IMPORTANT: ignore display:none, just check the table itself
        const rentWrap = $('#rent-addresses', panel);
        const emptyRow = rentWrap
          ? rentWrap.querySelector('#rent-empty-row, .rent-empty-row')
          : null;
        if (emptyRow) {
          errs.push(errorUnder(rentWrap, 'Please add at least one rent address.'));
        }
      }
    }

    if (errs.length) {
      showBanner();
      const first = errs[0];
      if (first) {
        const focusable = first.querySelector('input,select,textarea,button') || first;
        focusable?.focus?.();
        first.scrollIntoView({behavior:'smooth', block:'center'});
      }
      return false;
    }
    return true;
  }

  /* ----------------------------------------------------------
     LIVE CLEANUP
     ---------------------------------------------------------- */
  panel.addEventListener('input', function(e){
  const host = e.target.closest('.fi-group, .yn-group, .qs-block, #rent-addresses');
  if (host?.classList.contains('is-invalid')) {
    host.classList.remove('is-invalid');
    host.querySelector('.fi-error-text')?.remove();
  }

    if (e.target.id === 'moved_date_display') {
    const g = e.target.closest('.fi-group');
    if (g && g.classList.contains('has-date-error')) {
      g.classList.remove('has-date-error');
    }
  }

  // if fixing world income, remove tall class on BOTH columns
  if (e.target.id === 'inc_y1' || e.target.id === 'inc_y2' || e.target.id === 'inc_y3') {
    // right side
    const incomeRow = e.target.closest('.wi-row');
    if (incomeRow && e.target.value.trim() !== '') {
      incomeRow.classList.remove('wi-row-error');

      // figure out index of this row among income rows
      const incomeRows = Array.from(panel.querySelectorAll('.wi-col--income .wi-row'));
      const idx = incomeRows.indexOf(incomeRow);
      if (idx > -1) {
        const periodRows = panel.querySelectorAll('.wi-col--period .wi-row');
        const periodRow = periodRows[idx];
        if (periodRow) periodRow.classList.remove('wi-row-error');
      }
    }
  }

  if (!panel.querySelector('.is-invalid')) hideBanner();
}, true);


  /* ----------------------------------------------------------
     HOOK CONTINUE
     ---------------------------------------------------------- */
  const nextBtn = panel.querySelector('.continue-btn[data-goto="next"]');
  if (nextBtn) {
    nextBtn.addEventListener('click', function(ev){
      if (!validateTaxPanel()) {
        ev.preventDefault();
        ev.stopPropagation();
        ev.stopImmediatePropagation();
      }
    }, true);
  }

  // init
  toggleFirstTime();
  toggleMoved();
  toggleMovExp();
  toggleFTHB();
  toggleClaimFull();
  toggleRent();
  ensureBanner();
})();
</script>



<!--  ERROR NAV  -->

<script>
(function () {
  if (window.__PI_STEP_GUARD) return;
  window.__PI_STEP_GUARD = true;

  // --- state ----------------------------------------------------
  const visited   = new Set(); // panels we've ever shown
  const completed = new Set(); // panels we've moved *forward* from
  let furthestIdx = 0;         // index of furthest step we've reached

  // Helpers
  function currentKey() {
    const el = document.querySelector('.pi-main[data-panel]:not([hidden])');
    return el ? el.getAttribute('data-panel') : 'personal';
  }

  function sidebarOrder() {
    const arr = [];
    document.querySelectorAll('.pi-steps [data-step]').forEach(a => {
      const k = a.getAttribute('data-step');
      if (k && k !== 'pre') arr.push(k);
    });
    // fallback if somehow empty
    return arr.length ? arr : [
      'personal','tax','spouse','spouse-tax','children',
      'other-income','upload-self','upload-spouse','review','confirm'
    ];
  }

  function stepsNow() {
    try {
      if (window.App && typeof window.App.activeSteps === 'function') {
        const s = window.App.activeSteps();
        if (Array.isArray(s) && s.length) return s;
      }
    } catch (e) {}
    return sidebarOrder();
  }

  // Decide if we are allowed to go to "key" from sidebar/mobile
  function canGoTo(key) {
    if (!key || key === 'pre') return false;

    // Welcome / pre is always allowed via special buttons
    if (key === 'welcome') return true;

    const steps = stepsNow();
    const idx   = steps.indexOf(key);
    if (idx === -1) return false;

    // You may go to any step whose index <= furthestIdx (already reached before)
    return idx <= furthestIdx;
  }

  // --- patch showPanel + updateProgress after main app exists ----
  document.addEventListener('DOMContentLoaded', function () {
    if (!window.App || typeof window.App.showPanel !== 'function') return;

    const origShow    = window.App.showPanel.bind(window.App);
    const origUpdate  = (window.App.updateProgress || function(){}).bind(window.App);

    // Init state from wherever we land
    (function initState(){
      const steps = stepsNow();
      const cur   = currentKey();
      const idx   = steps.indexOf(cur);
      if (idx >= 0) {
        visited.add(cur);
        furthestIdx = idx;
      }
    })();

    function patchedUpdateProgress(currentKeyParam) {
      const cur = currentKeyParam || currentKey();
      const steps = stepsNow();

      // Re-sync furthestIdx with visited when flags change
      steps.forEach((k, i) => {
        if (visited.has(k) && i > furthestIdx) furthestIdx = i;
      });

      const sidebar = document.querySelector('.pi-steps');
      if (sidebar) {
        document.querySelectorAll('.pi-steps [data-step]').forEach(el => {
          const key = el.dataset.step;
          if (key === 'pre') return;

          if (!steps.includes(key)) {
            el.style.display = 'none';
            return;
          }
          el.style.display = '';

          el.classList.remove('is-current','is-done','is-locked');

          const idx = steps.indexOf(key);
          const curIdx = steps.indexOf(cur);
          const isCompleted = completed.has(key);
          const reachable   = idx <= furthestIdx;

          if (key === cur) {
            el.classList.add('is-current');
          } else if (isCompleted || idx < curIdx) {
            // once completed, it always keeps the check
            el.classList.add('is-done');
          }

          // Only steps beyond furthestIdx are "locked"
          if (!reachable && !el.classList.contains('is-current') && !el.classList.contains('is-done')) {
            el.classList.add('is-locked');
          }
        });
      }

      // Let existing code do anything else it needs
      try { origUpdate(cur); } catch(e) {}
    }

    // Override showPanel
    window.App.showPanel = function (targetKey) {
      const steps = stepsNow();
      const cur   = currentKey();
      const curIdx = steps.indexOf(cur);
      const tgtIdx = steps.indexOf(targetKey);

      if (curIdx >= 0) visited.add(cur);

      // If we move FORWARD in the flow, mark current as completed
      if (tgtIdx >= 0 && curIdx >= 0 && tgtIdx > curIdx) {
        completed.add(cur);
        if (tgtIdx > furthestIdx) furthestIdx = tgtIdx;
      }

      // Call original
      origShow(targetKey);

      // Mark target as visited
      if (tgtIdx >= 0) {
        visited.add(targetKey);
        if (tgtIdx > furthestIdx) furthestIdx = tgtIdx;
      }

      patchedUpdateProgress(targetKey);
    };

    // Replace updateProgress with patched version
    window.App.updateProgress = patchedUpdateProgress;

    // Initial paint
    setTimeout(() => patchedUpdateProgress(currentKey()), 0);
  });

  // --- intercept SIDEBAR clicks (back & allowed forwards only) ----
  document.addEventListener('click', function (e) {
    const link = e.target.closest('.pi-steps .pi-step[data-step]');
    if (!link) return;

    const key = link.dataset.goto || link.dataset.step;
    if (!key || key === 'pre') return;

    if (!canGoTo(key)) {
      // future step that has never been reached → block
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    }
    // if allowed, do nothing here – your original sidebar handler + App.showPanel run as usual
  }, true);

  // --- intercept MOBILE drawer clicks similarly -------------------
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('#pi-mb-nav .pi-mb-link[data-goto]');
    if (!btn) return;

    let key = btn.getAttribute('data-goto');
    if (key === 'upload') {
      // same logic your mobile script uses
      const f = (window.App && window.App.flags) ? window.App.flags() : {};
      key = f.spouseFiles ? 'upload-spouse' : 'upload-self';
    }

    if (!canGoTo(key)) {
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    }
  }, true);

})();
</script>


<script>
(function () {
  // Run when DOM is ready
  function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  // Master order of steps (same as your ORDER)
  const ORDER = [
    'personal',
    'tax',
    'spouse',
    'spouse-tax',
    'children',
    'other-income',
    'upload-self',
    'upload-spouse',
    'review',
    'confirm'
  ];

  // Steps that are COMPLETED (user clicked Continue → Next there)
  const doneSteps = new Set();

  // Furthest step index user has ever visited (in ORDER)
  let visitedMax = 0;

  // --- 1) Track furthest visited step via pi:panel-changed ---
  document.addEventListener('pi:panel-changed', function (e) {
    const key = e.detail && e.detail.panel;
    const idx = ORDER.indexOf(key);
    if (idx >= 0 && idx > visitedMax) {
      visitedMax = idx;
    }
  });

  // --- 2) Mark step as DONE when user clicks Continue → Next ---
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('#form-panel .continue-btn[data-goto="next"]');
    if (!btn) return;

    const currentPanel = document.querySelector(
      '#form-panel .pi-main[data-panel]:not([hidden])'
    );
    if (!currentPanel) return;

    const key = currentPanel.dataset.panel;
    if (key) {
      doneSteps.add(key);   // ✅ keep checkmark forever
    }
  }, true); // capture so it always fires

  // --- 3) Override App.updateProgress to use DONE + VISITED logic ---
  onReady(function () {
    if (!window.App || typeof window.App.updateProgress !== 'function') return;

    const originalUpdate = window.App.updateProgress.bind(window.App);
    const sidebar   = document.querySelector('.pi-steps');
    const formPanel = document.getElementById('form-panel');

    window.App.updateProgress = function (currentKey) {
      if (!sidebar) {
        // Fallback to original if sidebar missing
        originalUpdate(currentKey);
        return;
      }

      // Active steps (respect marital / spouse / children logic)
      let stepsActive = [];
      try {
        stepsActive = (window.App.activeSteps && window.App.activeSteps()) || [];
      } catch (e) {}
      if (!stepsActive || !stepsActive.length) {
        stepsActive = ORDER.slice();
      }

      // Ensure visitedMax is at least the current step
      const curOrderIdx = ORDER.indexOf(currentKey);
      if (curOrderIdx >= 0 && curOrderIdx > visitedMax) {
        visitedMax = curOrderIdx;
      }
      const maxAllowedIndex = visitedMax;

      sidebar.querySelectorAll('[data-step]').forEach(el => {
        const key = el.dataset.step;
        if (!key) return;

        // Hide steps that are not active (e.g. spouse steps when single)
        if (key !== 'pre' && !stepsActive.includes(key)) {
          el.style.display = 'none';
          return;
        }
        el.style.display = '';

        // Reset classes
        el.classList.remove('is-current', 'is-done', 'is-locked');
        el.removeAttribute('aria-current');

        // "Pre-details" logic stays the same
        if (key === 'pre') {
          if (formPanel && formPanel.style.display !== 'none') {
            el.classList.add('is-done');
          }
          return;
        }

        const orderIndex = ORDER.indexOf(key);
        const canVisit   = (orderIndex <= maxAllowedIndex);
        const isCurrent  = (key === currentKey);
        const isDone     = doneSteps.has(key);

        // Current step highlight
        if (isCurrent) {
          el.classList.add('is-current');
          el.setAttribute('aria-current', 'step');
        }
        // Persist checkmarks only for truly done steps
        if (isDone) {
          el.classList.add('is-done');
        }

        // Lock ONLY steps beyond furthest visited
        if (!canVisit && !isCurrent) {
          el.classList.add('is-locked');
        }
      });
    };

    // Initial sync (in case you enter in the middle)
    const firstPanel = document.querySelector('#form-panel .pi-main[data-panel]:not([hidden])');
    if (firstPanel) {
      window.App.updateProgress(firstPanel.dataset.panel);
    }
  });
})();
</script>








<?php
session_start();
include '../auth/config.php';


// ---------- confirm panel flag ----------
$showConfirmPanel = !empty($_SESSION['show_confirm_panel']);
if ($showConfirmPanel) {
    // one-time use
    unset($_SESSION['show_confirm_panel']);
}


// ---------------------------------------------------------
//  Helpers
// ---------------------------------------------------------
function field($name, $default = null) {
    if (!isset($_POST[$name])) return $default;
    $v = $_POST[$name];

    // If it’s an array, don’t try to trim it
    if (is_array($v)) return $default;

    return trim($v);
}

function parseDateField($name) {
    if (empty($_POST[$name])) return null;

    // original raw value from the form (e.g. "19 | Nov | 2003")
    $raw = trim($_POST[$name]);

    // normalize: turn pipes into spaces and compress multiple spaces
    // "19 | Nov | 2003" -> "19 Nov 2003"
    $norm = preg_replace('/\s+/', ' ', str_replace('|', ' ', $raw));

    // Try a few formats safely
    $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'd M Y', 'd M, Y'];

    // 1) try normalized string
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $norm);
        if ($dt instanceof DateTime) {
            return $dt->format('Y-m-d');  // store as YYYY-MM-DD
        }
    }

    // 2) last resort: strtotime on normalized value
    $ts = strtotime($norm);
    if ($ts !== false) {
        return date('Y-m-d', $ts);
    }

    // if cannot be parsed, store NULL instead of garbage
    return null;
}


/**
 * For DECIMAL columns.
 * - empty string / missing  => NULL
 * - "1,234.56"              => "1234.56"
 */
function decimalField($name) {
    $raw = field($name);

    // 1) Not present or empty => NULL
    if ($raw === '' || $raw === null) {
        return null;
    }

    // 2) Normalize: remove commas and spaces (e.g. "1,234.56")
    $raw = str_replace([',', ' '], '', $raw);

    // 3) Strip anything that isn't digit / dot / minus
    $raw = preg_replace('/[^0-9.\-]/', '', $raw);

    // 4) If still not numeric, treat as NULL
    if ($raw === '' || !is_numeric($raw)) {
        return null;
    }

    // 5) Return as string, PDO/MySQL will handle it
    return $raw;
}


/**
 * For INT columns like owner_count.
 */
function intField($name) {
    $raw = field($name);
    if ($raw === '' || $raw === null) {
        return null;
    }
    return (int)$raw;
}

/**
 * For JSON columns (children_json, rent_addresses_json, rental_props_json).
 * If the field is missing or empty string, we return a default valid JSON string.
 */
function jsonField($name, $default = '[]') {
    if (!isset($_POST[$name])) {
        return $default;
    }
    $v = trim($_POST[$name]);
    if ($v === '') {
        return $default;
    }
    return $v; // assume JS sent valid JSON
}

function handleMultiUpload($fieldName, $subdir, $userFolderBase, &$errors = []) {
    if (empty($_FILES[$fieldName]) || !is_array($_FILES[$fieldName]['name'])) {
        return [];
    }

    $results = [];

    // base directory for this group
    $targetBase = rtrim($userFolderBase, '/').'/'.$subdir;
    if (!is_dir($targetBase)) {
        mkdir($targetBase, 0775, true);
    }

    $names      = $_FILES[$fieldName]['name'];
    $tmp        = $_FILES[$fieldName]['tmp_name'];
    $errorsCode = $_FILES[$fieldName]['error'];
    $sizes      = $_FILES[$fieldName]['size'];

    // ---------------- NEW: grab password meta arrays ----------------
    $pwProtKey = $fieldName . '_pw_protected';
    $pwKey     = $fieldName . '_pw';

    $pwProtectedList = (isset($_POST[$pwProtKey]) && is_array($_POST[$pwProtKey]))
        ? array_values($_POST[$pwProtKey])
        : [];

    $pwList = (isset($_POST[$pwKey]) && is_array($_POST[$pwKey]))
        ? array_values($_POST[$pwKey])
        : [];
    // ---------------------------------------------------------------

    foreach ($names as $i => $origName) {
        if ($errorsCode[$i] === UPLOAD_ERR_NO_FILE || $origName === '') {
            continue; // user didn't select a file in this slot
        }

        if ($errorsCode[$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading file '$origName' (code {$errorsCode[$i]})";
            continue;
        }

        // Simple size guard (e.g. 10 MB)
        if ($sizes[$i] > 10 * 1024 * 1024) {
            $errors[] = "File '$origName' is too large (max 10MB).";
            continue;
        }

        $safeOrig = preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
        $newName  = uniqid().'-'.$safeOrig;
        $target   = $targetBase.'/'.$newName;

        if (!move_uploaded_file($tmp[$i], $target)) {
            $errors[] = "Failed to move uploaded file '$origName'.";
            continue;
        }

        // -------- NEW: attach password info --------
        $protRaw = $pwProtectedList[$i] ?? '';   // 'yes' | 'no' | ''
        $pwRaw   = $pwList[$i] ?? '';           // user-typed password or ''

        $isProtected = ($protRaw === 'yes');

        // encrypt only if password is provided + marked protected
        $pwStored = null;
        if ($isProtected && $pwRaw !== '') {
            $pwStored = encrypt_decrypt('encrypt', $pwRaw);
        }
        // -------------------------------------------

        $results[] = [
            'original'       => $origName,
            'stored'         => $target,
            'size'           => $sizes[$i],
            'pw_protected'   => $isProtected ? 'yes' : ($protRaw === 'no' ? 'no' : ''),
            'pw_encrypted'   => $pwStored,
        ];
    }

    return $results;
}


// ---------------------------------------------------------
//  SESSION CHECK
// ---------------------------------------------------------
if (!isset($_SESSION['email'])) {
    header('location:../auth');
    exit();
}

$loginEmail = $_SESSION['email'];

// ---------------------------------------------------------
//  FETCH BASE USER (from your existing users table)
// ---------------------------------------------------------
$userRow = null;
try {
    $stmtUser = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmtUser->execute([$loginEmail]);
    $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // if users table doesn't exist or something, just ignore
    $userRow = null;
}

// ---------------------------------------------------------
//  FETCH or CREATE personal_tax ROW
// ---------------------------------------------------------
$stmtTax = $db->prepare('SELECT * FROM personal_tax WHERE email = ? LIMIT 1');
$stmtTax->execute([$loginEmail]);
$rowTax = $stmtTax->fetch(PDO::FETCH_ASSOC);

if (!$rowTax) {
    // First time here → create minimal row seeded from users table/session
    $insert = $db->prepare("
        INSERT INTO personal_tax (
          user_id, email,
          first_name, last_name,
          phone_plain, email_display,
          created_at, updated_at
        ) VALUES (
          :user_id, :email,
          :first_name, :last_name,
          :phone_plain, :email_display,
          NOW(), NOW()
        )
    ");

    $insert->execute([
        ':user_id'       => $userRow['id']         ?? null,
        ':email'         => $loginEmail,
        ':first_name'    => $userRow['first_name'] ?? ($_SESSION['first_name'] ?? ''),
        ':last_name'     => $userRow['last_name']  ?? ($_SESSION['last_name']  ?? ''),
        ':phone_plain'   => $userRow['phone']      ?? ($_SESSION['phone']      ?? ''),
        ':email_display' => $loginEmail,
    ]);

    // Re-load row
    $stmtTax->execute([$loginEmail]);
    $rowTax = $stmtTax->fetch(PDO::FETCH_ASSOC);
}

$taxId = $rowTax['id'];

// ---------------------------------------------------------
//  HANDLE POST (SAVE FORM)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  error_log('FULL POST: ' . print_r($_POST, true));
    // ---------- PERSONAL ----------
    $first_name   = field('first_name');
    $middle_name  = field('middle_name');
    $last_name    = field('last_name');
    $dob          = parseDateField('dob');  // stored as DATE in DB (not encrypted)
    $gender       = field('gender');

    $street       = field('street');
    $unit         = field('unit');
    $city         = field('city');
    $province     = field('province');
    $postal       = field('postal');
    $country      = field('country');

    $phone_plain  = field('phone');              // form name is "phone"
    $email_disp   = field('email');              // separate from login email
    $sin          = field('sin');

    // ---------- WELCOME / MARITAL ----------
    $marital_status   = field('marital_status');
    $status_date      = parseDateField('status_date');      // married/common-law
    $status_date_sdw  = parseDateField('status_date_sdw');  // separated/divorced/widowed

    $spouse_in_canada = field('spouse_in_canada');          // Yes / No
    $spouseFile       = field('spouseFile');                // yes / no
    $children_flag    = field('children');                  // yes / no

    // ---------- TAX PANEL ----------
    $first_time       = field('first_time');
    $paragon_prior    = field('paragon_prior');
    $return_years     = field('return_years');

    $entry_date       = parseDateField('entry_date');       // hidden YYYY-MM-DD
    $birth_country    = field('birth_country');

    // DECIMAL
    $inc_y1           = decimalField('inc_y1');
    $inc_y2           = decimalField('inc_y2');
    $inc_y3           = decimalField('inc_y3');

    $moved_province   = field('moved_province');
    $moved_date       = parseDateField('moved_date');       // hidden YYYY-MM-DD
    $prov_from        = field('prov_from');
    $prov_to          = field('prov_to');

    $moving_expenses_claim = field('moving_expenses_claim');
    $moving_prev_address   = field('moving_prev_address');
    $moving_distance       = field('moving_distance');

    $first_home_buyer    = field('first_home_buyer');
    $first_home_purchase = parseDateField('first_home_purchase');
    $claim_full          = field('claim_full');
    $owner_count         = intField('owner_count');         // INT helper

    $onRent            = field('onRent');
    $claimRent         = field('claimRent');

    // JSON from your JS (rent addresses)
    $rent_addresses_json = jsonField('rent_addresses_json', '[]');

    // ---------- SPOUSE PANEL ----------
    $spouse_first_name        = field('spouse_first_name');
    $spouse_middle_name       = field('spouse_middle_name');
    $spouse_last_name         = field('spouse_last_name');
    $spouse_dob               = parseDateField('spouse_dob'); // DATE in DB (not encrypted)

    // we mirror top-level question
    $spouse_in_canada_flag    = $spouse_in_canada;

    // DECIMAL
    $spouse_income_outside_cad = decimalField('spouse_income_outside_cad');
    $spouse_income_cad         = decimalField('spouse_income_cad');

    error_log('RAW spouse_income_cad: ' . ($_POST['spouse_income_cad'] ?? 'MISSING'));
    error_log('FINAL spouse_income_cad: ' . var_export($spouse_income_cad, true));

    $spouse_sin           = field('spouse_sin');
    $spouse_address_same  = field('spouse_address_same');  // Yes/No

    $spouse_street        = field('spouse_street');
    $spouse_unit          = field('spouse_unit');
    $spouse_city          = field('spouse_city');
    $spouse_province      = field('spouse_province');
    $spouse_postal        = field('spouse_postal');
    $spouse_country       = field('spouse_country');

    $spouse_phone         = field('spouse_phone');
    $spouse_email         = field('spouse_email');

    // ---------- SPOUSE TAX PANEL ----------
    $sp_first_time    = field('sp_first_time');
    $sp_paragon_prior = field('sp_paragon_prior');
    $sp_return_years  = field('sp_return_years');

    $sp_entry_date    = parseDateField('sp_entry_date');
    $sp_birth_country = field('sp_birth_country'); // make name="sp_birth_country" in HTML

    // DECIMAL
    $sp_inc_y1        = decimalField('sp_inc_y1');
    $sp_inc_y2        = decimalField('sp_inc_y2');
    $sp_inc_y3        = decimalField('sp_inc_y3');

    $sp_moved_province = field('sp_moved_province');
    $sp_moved_date     = parseDateField('sp_moved_date');
    $sp_prov_from      = field('sp_prov_from');
    $sp_prov_to        = field('sp_prov_to');

    // ---------- CHILDREN (JSON) ----------
    error_log('RAW children_json POST: ' . (isset($_POST['children_json']) ? $_POST['children_json'] : 'MISSING'));

    $children_json = '[]';

    if (!empty($_POST['child_rows']) && is_array($_POST['child_rows'])) {
        $list = [];

        foreach ($_POST['child_rows'] as $row) {
            $first = isset($row['first_name']) ? trim($row['first_name']) : '';
            $last  = isset($row['last_name'])  ? trim($row['last_name'])  : '';
            $dob   = isset($row['dob'])        ? trim($row['dob'])        : '';
            $inCan = isset($row['in_canada'])  ? trim($row['in_canada'])  : '';

            // skip completely empty rows
            if ($first === '' && $last === '' && $dob === '') {
                continue;
            }

            $list[] = [
                'first_name'  => $first,
                'last_name'   => $last,
                'dob'         => $dob,           // ISO from JS
                'dob_display' => $dob,           // or keep separate if you want
                'in_canada'   => $inCan !== '' ? $inCan : 'Yes',
            ];
        }

        if (!empty($list)) {
            $children_json = json_encode($list, JSON_UNESCAPED_SLASHES);
        }
    } else {
        $children_json = jsonField('children_json', '[]');
    }

    error_log('children_json going into DB: ' . $children_json);

    // ---------- OTHER INCOME ----------
    $gig_income           = field('gig_income');
    $gig_expenses_summary = field('gig_expenses_summary');
    $gig_hst              = field('gig_hst');
    $hst_number           = field('hst_number');
    $hst_access           = field('hst_access');
    $hst_start            = parseDateField('hst_start');
    $hst_end              = parseDateField('hst_end');

    $sp_gig_income           = field('sp_gig_income');
    $sp_gig_expenses_summary = field('sp_gig_expenses_summary');
    $sp_gig_hst              = field('sp_gig_hst');
    $sp_hst_number           = field('sp_hst_number');
    $sp_hst_access           = field('sp_hst_access');
    $sp_hst_start            = parseDateField('sp_hst_start');
    $sp_hst_end              = parseDateField('sp_hst_end');

    // ---------- RENTAL PROPERTIES (JSON) ----------
    $rental_props_json = '[]';

    if (!empty($_POST['rental_props']) && is_array($_POST['rental_props'])) {

        $props = [];

        foreach ($_POST['rental_props'] as $idx => $row) {
            if (!is_array($row)) continue;

            $owner       = isset($row['owner'])        ? trim($row['owner'])        : '';
            $address     = isset($row['address'])      ? trim($row['address'])      : '';
            $startDisp   = isset($row['start_display'])? trim($row['start_display']): '';
            $endDisp     = isset($row['end_display'])  ? trim($row['end_display'])  : '';
            $partner     = isset($row['partner'])      ? trim($row['partner'])      : '';
            $ownerPct    = isset($row['owner_pct'])    ? trim($row['owner_pct'])    : '';
            $ownUsePct   = isset($row['ownuse_pct'])   ? trim($row['ownuse_pct'])   : '';
            $grossIncome = isset($row['gross'])        ? trim($row['gross'])        : '';

            $exp = isset($row['expenses']) && is_array($row['expenses'])
                ? $row['expenses']
                : [];

            $props[] = [
                'owner'          => $owner,
                'address'        => $address,
                'start_display'  => $startDisp,
                'end_display'    => $endDisp,
                'partner'        => $partner,
                'owner_pct'      => $ownerPct,
                'own_use_pct'    => $ownUsePct,
                'gross_income'   => $grossIncome,
                'exp_mortgage'   => trim($exp['mortgage']     ?? ''),
                'exp_insurance'  => trim($exp['insurance']    ?? ''),
                'exp_repairs'    => trim($exp['repairs']      ?? ''),
                'exp_utilities'  => trim($exp['utilities']    ?? ''),
                'exp_internet'   => trim($exp['internet']     ?? ''),
                'exp_propertytax'=> trim($exp['property_tax'] ?? ''),
                'exp_other'      => trim($exp['other']        ?? ''),
            ];
        }

        if (!empty($props)) {
            $rental_props_json = json_encode($props, JSON_UNESCAPED_SLASHES);
        }

    } else {
        $rental_props_json = jsonField('rental_props_json', '[]');
    }

    error_log('DEBUG rental_props (array): ' . (isset($_POST['rental_props'])
        ? print_r($_POST['rental_props'], true)
        : 'MISSING'));
    error_log('FINAL rental_props_json going into DB: ' . $rental_props_json);


    // ---------- FILE UPLOADS ----------
    $userUploadFolder = __DIR__ . '/../uploads/tax/user_' . $taxId;

    $uploadErrors = [];

    /**
     * Applicant uploads
     */
    $appUploads = [
        'id_proof'   => [],
        'tslips'     => [],
        't2202'      => [],
        'invest'     => [],
        't2200'      => [],
        'exp_summary'=> [],
        'otherdocs'  => [],
        'gig'        => [],
    ];

    $gigFiles = handleMultiUpload('gig_tax_summary', 'app_gig_tax', $userUploadFolder, $uploadErrors);
    if ($gigFiles) {
        $appUploads['gig'] = $gigFiles;
    }

    $idProofFiles = handleMultiUpload('app_id_proof', 'app_id_proof', $userUploadFolder, $uploadErrors);
    if ($idProofFiles) {
        $appUploads['id_proof'] = $idProofFiles;
    }

    $tslipsFiles = handleMultiUpload('app_tslips', 'app_tslips', $userUploadFolder, $uploadErrors);
    if ($tslipsFiles) {
        $appUploads['tslips'] = $tslipsFiles;
    }

    $t2202Files = handleMultiUpload('app_t2202_receipt', 'app_t2202', $userUploadFolder, $uploadErrors);
    if ($t2202Files) {
        $appUploads['t2202'] = $t2202Files;
    }

    $investFiles = handleMultiUpload('app_invest', 'app_invest', $userUploadFolder, $uploadErrors);
    if ($investFiles) {
        $appUploads['invest'] = $investFiles;
    }

    $t2200Files = handleMultiUpload('app_t2200_work', 'app_t2200_work', $userUploadFolder, $uploadErrors);
    if ($t2200Files) {
        $appUploads['t2200'] = $t2200Files;
    }

    $expSummaryFiles = handleMultiUpload('app_exp_summary', 'app_exp_summary', $userUploadFolder, $uploadErrors);
    if ($expSummaryFiles) {
        $appUploads['exp_summary'] = $expSummaryFiles;
    }

    $otherDocsFiles = handleMultiUpload('app_otherdocs', 'app_otherdocs', $userUploadFolder, $uploadErrors);
    if ($otherDocsFiles) {
        $appUploads['otherdocs'] = $otherDocsFiles;
    }

    /**
     * Spouse uploads
     */
    $spouseUploads = [
        'id_proof'  => [],
        'tslips'    => [],
        't2202'     => [],
        'invest'    => [],
        'otherdocs' => [],
        'gig'       => [],
    ];

    $spGigFiles = handleMultiUpload('sp_gig_tax_summary', 'sp_gig_tax', $userUploadFolder, $uploadErrors);
    if ($spGigFiles) {
        $spouseUploads['gig'] = $spGigFiles;
    }

    $spIdProofFiles = handleMultiUpload('sp_id_proof', 'sp_id_proof', $userUploadFolder, $uploadErrors);
    if ($spIdProofFiles) {
        $spouseUploads['id_proof'] = $spIdProofFiles;
    }

    $spT2202Files = handleMultiUpload('sp_t2202', 'sp_t2202', $userUploadFolder, $uploadErrors);
    if ($spT2202Files) {
        $spouseUploads['t2202'] = $spT2202Files;
    }

    $spTslipsFiles = handleMultiUpload('sp_tslips', 'sp_tslips', $userUploadFolder, $uploadErrors);
    if ($spTslipsFiles) {
        $spouseUploads['tslips'] = $spTslipsFiles;
    }

    $spInvestFiles = handleMultiUpload('sp_invest', 'sp_invest', $userUploadFolder, $uploadErrors);
    if ($spInvestFiles) {
        $spouseUploads['invest'] = $spInvestFiles;
    }

    $spOtherDocsFiles = handleMultiUpload('sp_otherdocs', 'sp_otherdocs', $userUploadFolder, $uploadErrors);
    if ($spOtherDocsFiles) {
        $spouseUploads['otherdocs'] = $spOtherDocsFiles;
    }

    $app_uploads_json    = json_encode($appUploads, JSON_UNESCAPED_SLASHES);
    $spouse_uploads_json = json_encode($spouseUploads, JSON_UNESCAPED_SLASHES);

    // -------------------------------------------------
    //  ENCRYPT SENSITIVE FIELDS (SIN only)
    // -------------------------------------------------
    // *** NEW: encrypt applicant SIN & spouse SIN before saving to DB
    $sin_encrypted        = $sin        ? encrypt_decrypt('encrypt', $sin)        : null;   // ***
    $spouse_sin_encrypted = $spouse_sin ? encrypt_decrypt('encrypt', $spouse_sin) : null;   // ***
    // (DOBs remain plain DATE columns so you can still do date logic in SQL)

    // -------------------------------------------------
    //  UPDATE personal_tax
    // -------------------------------------------------
    $updateSql = "
      UPDATE personal_tax SET
        /* PERSONAL */
        first_name      = :first_name,
        middle_name     = :middle_name,
        last_name       = :last_name,
        dob             = :dob,
        gender          = :gender,
        street          = :street,
        unit            = :unit,
        city            = :city,
        province        = :province,
        postal          = :postal,
        country         = :country,
        phone_plain     = :phone_plain,
        sin             = :sin,
        email_display   = :email_display,

        /* WELCOME / MARITAL */
        marital_status       = :marital_status,
        status_date          = :status_date,
        status_date_sdw      = :status_date_sdw,
        spouse_in_canada     = :spouse_in_canada,
        spouseFile           = :spouseFile,
        children_flag        = :children_flag,

        /* TAX PANEL */
        first_time           = :first_time,
        paragon_prior        = :paragon_prior,
        return_years         = :return_years,

        entry_date           = :entry_date,
        birth_country        = :birth_country,
        inc_y1               = :inc_y1,
        inc_y2               = :inc_y2,
        inc_y3               = :inc_y3,

        moved_province       = :moved_province,
        moved_date           = :moved_date,
        prov_from            = :prov_from,
        prov_to              = :prov_to,
        moving_expenses_claim = :moving_expenses_claim,
        moving_prev_address  = :moving_prev_address,
        moving_distance      = :moving_distance,

        first_home_buyer     = :first_home_buyer,
        first_home_purchase  = :first_home_purchase,
        claim_full           = :claim_full,
        owner_count          = :owner_count,

        onRent               = :onRent,
        claimRent            = :claimRent,
        rent_addresses_json  = :rent_addresses_json,

        /* SPOUSE PANEL */
        spouse_first_name        = :spouse_first_name,
        spouse_middle_name       = :spouse_middle_name,
        spouse_last_name         = :spouse_last_name,
        spouse_dob               = :spouse_dob,
        spouse_in_canada_flag    = :spouse_in_canada_flag,
        spouse_income_outside_cad = :spouse_income_outside_cad,

        spouse_sin               = :spouse_sin,
        spouse_address_same      = :spouse_address_same,
        spouse_street            = :spouse_street,
        spouse_unit              = :spouse_unit,
        spouse_city              = :spouse_city,
        spouse_province          = :spouse_province,
        spouse_postal            = :spouse_postal,
        spouse_country           = :spouse_country,
        spouse_phone             = :spouse_phone,
        spouse_email             = :spouse_email,
        spouse_income_cad        = :spouse_income_cad,

        /* SPOUSE TAX */
        sp_first_time        = :sp_first_time,
        sp_paragon_prior     = :sp_paragon_prior,
        sp_return_years      = :sp_return_years,
        sp_entry_date        = :sp_entry_date,
        sp_birth_country     = :sp_birth_country,
        sp_inc_y1            = :sp_inc_y1,
        sp_inc_y2            = :sp_inc_y2,
        sp_inc_y3            = :sp_inc_y3,
        sp_moved_province    = :sp_moved_province,
        sp_moved_date        = :sp_moved_date,
        sp_prov_from         = :sp_prov_from,
        sp_prov_to           = :sp_prov_to,

        /* CHILDREN JSON */
        children_json        = :children_json,

        /* OTHER INCOME */
        gig_income           = :gig_income,
        gig_expenses_summary = :gig_expenses_summary,
        gig_hst              = :gig_hst,
        hst_number           = :hst_number,
        hst_access           = :hst_access,
        hst_start            = :hst_start,
        hst_end              = :hst_end,

        sp_gig_income           = :sp_gig_income,
        sp_gig_expenses_summary = :sp_gig_expenses_summary,
        sp_gig_hst              = :sp_gig_hst,
        sp_hst_number           = :sp_hst_number,
        sp_hst_access           = :sp_hst_access,
        sp_hst_start            = :sp_hst_start,
        sp_hst_end              = :sp_hst_end,

        /* RENTAL PROPS JSON */
        rental_props_json    = :rental_props_json,

        /* UPLOADS JSON */
        app_uploads_json     = :app_uploads_json,
        spouse_uploads_json  = :spouse_uploads_json,

        updated_at = NOW()
      WHERE id = :id
    ";

    $update = $db->prepare($updateSql);

    $update->execute([
        // PERSONAL
        ':first_name'      => $first_name,
        ':middle_name'     => $middle_name,
        ':last_name'       => $last_name,
        ':dob'             => $dob,                 // not encrypted (DATE column)
        ':gender'          => $gender,
        ':street'          => $street,
        ':unit'            => $unit,
        ':city'            => $city,
        ':province'        => $province,
        ':postal'          => $postal,
        ':country'         => $country,
        ':phone_plain'     => $phone_plain,
        ':sin'             => $sin_encrypted,       // *** save encrypted SIN
        ':email_display'   => $email_disp,

        // WELCOME / MARITAL
        ':marital_status'   => $marital_status,
        ':status_date'      => $status_date,
        ':status_date_sdw'  => $status_date_sdw,
        ':spouse_in_canada' => $spouse_in_canada,
        ':spouseFile'       => $spouseFile,
        ':children_flag'    => $children_flag,

        // TAX
        ':first_time'        => $first_time,
        ':paragon_prior'     => $paragon_prior,
        ':return_years'      => $return_years,
        ':entry_date'        => $entry_date,
        ':birth_country'     => $birth_country,
        ':inc_y1'            => $inc_y1,
        ':inc_y2'            => $inc_y2,
        ':inc_y3'            => $inc_y3,
        ':moved_province'    => $moved_province,
        ':moved_date'        => $moved_date,
        ':prov_from'         => $prov_from,
        ':prov_to'           => $prov_to,
        ':moving_expenses_claim' => $moving_expenses_claim,
        ':moving_prev_address'   => $moving_prev_address,
        ':moving_distance'       => $moving_distance,
        ':first_home_buyer'      => $first_home_buyer,
        ':first_home_purchase'   => $first_home_purchase,
        ':claim_full'            => $claim_full,
        ':owner_count'           => $owner_count,
        ':onRent'                => $onRent,
        ':claimRent'             => $claimRent,
        ':rent_addresses_json'   => $rent_addresses_json,

        // SPOUSE PANEL
        ':spouse_first_name'         => $spouse_first_name,
        ':spouse_middle_name'        => $spouse_middle_name,
        ':spouse_last_name'          => $spouse_last_name,
        ':spouse_dob'                => $spouse_dob, // DATE
        ':spouse_in_canada_flag'     => $spouse_in_canada_flag,
        ':spouse_income_outside_cad' => $spouse_income_outside_cad,
        ':spouse_sin'                => $spouse_sin_encrypted,   // *** save encrypted spouse SIN
        ':spouse_address_same'       => $spouse_address_same,
        ':spouse_street'             => $spouse_street,
        ':spouse_unit'               => $spouse_unit,
        ':spouse_city'               => $spouse_city,
        ':spouse_province'           => $spouse_province,
        ':spouse_postal'             => $spouse_postal,
        ':spouse_country'            => $spouse_country,
        ':spouse_phone'              => $spouse_phone,
        ':spouse_email'              => $spouse_email,
        ':spouse_income_cad'         => $spouse_income_cad,

        // SPOUSE TAX
        ':sp_first_time'    => $sp_first_time,
        ':sp_paragon_prior' => $sp_paragon_prior,
        ':sp_return_years'  => $sp_return_years,
        ':sp_entry_date'    => $sp_entry_date,
        ':sp_birth_country' => $sp_birth_country,
        ':sp_inc_y1'        => $sp_inc_y1,
        ':sp_inc_y2'        => $sp_inc_y2,
        ':sp_inc_y3'        => $sp_inc_y3,
        ':sp_moved_province'=> $sp_moved_province,
        ':sp_moved_date'    => $sp_moved_date,
        ':sp_prov_from'     => $sp_prov_from,
        ':sp_prov_to'       => $sp_prov_to,

        // CHILDREN
        ':children_json'    => $children_json,

        // OTHER INCOME
        ':gig_income'           => $gig_income,
        ':gig_expenses_summary' => $gig_expenses_summary,
        ':gig_hst'              => $gig_hst,
        ':hst_number'           => $hst_number,
        ':hst_access'           => $hst_access,
        ':hst_start'            => $hst_start,
        ':hst_end'              => $hst_end,
        ':sp_gig_income'           => $sp_gig_income,
        ':sp_gig_expenses_summary' => $sp_gig_expenses_summary,
        ':sp_gig_hst'              => $sp_gig_hst,
        ':sp_hst_number'           => $sp_hst_number,
        ':sp_hst_access'           => $sp_hst_access,
        ':sp_hst_start'            => $sp_hst_start,
        ':sp_hst_end'              => $sp_hst_end,

        // RENTAL PROPS + UPLOADS
        ':rental_props_json'   => $rental_props_json,
        ':app_uploads_json'    => $app_uploads_json,
        ':spouse_uploads_json' => $spouse_uploads_json,

        ':id' => $taxId
    ]);

    // After successful save, show confirm panel on next load
    $_SESSION['show_confirm_panel'] = true;

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

// ---------------------------------------------------------
//  RE-LOAD AFTER SAVE (or first load)
// ---------------------------------------------------------
$stmtTax->execute([$loginEmail]);
$rowTax = $stmtTax->fetch(PDO::FETCH_ASSOC);

// compatibility variables for your existing HTML
$rowUser      = $rowTax;  // personal panel uses $rowUser
$rowSpouse    = $rowTax;  // spouse panel used $rowSpouse
$rowSpouseTax = $rowTax;  // spouse-tax panel used $rowSpouseTax

// ---------- DECRYPT SIN VALUES FOR DISPLAY ----------
// *** NEW: we only decrypt into $rowUser / $rowSpouse that the HTML uses.
// The DB row ($rowTax) keeps the encrypted values.

if (!empty($rowUser['sin'])) {
    $rowUser['sin'] = encrypt_decrypt('decrypt', $rowUser['sin']);          // ***
}
if (!empty($rowSpouse['spouse_sin'])) {
    $rowSpouse['spouse_sin'] = encrypt_decrypt('decrypt', $rowSpouse['spouse_sin']); // ***
}

// JSON seeds for modals/tables
$childrenListJSON   = $rowTax['children_json']        ?: '[]';
$rentListJSON       = $rowTax['rent_addresses_json']  ?: '[]';
$rentalPropsJSON    = $rowTax['rental_props_json']    ?: '[]';
$appUploadsJSON     = $rowTax['app_uploads_json']     ?: '{}';
$spouseUploadsJSON  = $rowTax['spouse_uploads_json']  ?: '{}';

// small helper: ensure plain strings (no PHP NULL)
if ($childrenListJSON === null)  $childrenListJSON  = '[]';
if ($rentListJSON === null)      $rentListJSON      = '[]';
if ($rentalPropsJSON === null)   $rentalPropsJSON   = '[]';
if ($appUploadsJSON === null)    $appUploadsJSON    = '{}';
if ($spouseUploadsJSON === null) $spouseUploadsJSON = '{}';

function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/'; // your existing
    $secret_iv  = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';

    $key = hash('sha256', $secret_key);
    $iv  = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } elseif ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

// log (optional)
error_log("personal_tax loaded for: " . $loginEmail);
?>
