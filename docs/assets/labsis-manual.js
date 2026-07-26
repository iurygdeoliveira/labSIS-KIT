(function () {
  'use strict';

  function initBack() {
    document.querySelectorAll('[data-back]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var parent = btn.getAttribute('data-parent');
        var ref = document.referrer || '';
        var isInternal =
          ref &&
          (ref.indexOf('/docs/manual/') !== -1 || ref.indexOf('docs%2Fmanual') !== -1);

        if (window.history.length > 1 && isInternal) {
          window.history.back();
          return;
        }
        if (parent) {
          window.location.href = parent;
          return;
        }
        window.location.href = btn.getAttribute('data-home') || '../index.html';
      });
    });
  }

  function initCopyMd() {
    var btn = document.getElementById('copyMdBtn');
    var src = document.getElementById('source-markdown');
    if (!btn || !src) return;

    btn.addEventListener('click', function () {
      var text = src.textContent || '';
      function flash() {
        btn.textContent = 'Copiado ✓';
        btn.classList.add('copied');
        setTimeout(function () {
          btn.textContent = 'Copiar Markdown original';
          btn.classList.remove('copied');
        }, 1200);
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(flash, flash);
      } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try {
          document.execCommand('copy');
        } catch (err) {
          /* ignore */
        }
        document.body.removeChild(ta);
        flash();
      }
    });
  }

  function initSidebar() {
    var body = document.body;
    if (!body.classList.contains('has-sidebar')) return;

    var openBtn = document.querySelector('[data-sidebar-open]');
    var closeTargets = document.querySelectorAll('[data-sidebar-close]');
    var backdrop = document.querySelector('.sidebar-backdrop');

    function openSidebar() {
      body.classList.add('sidebar-open');
      if (backdrop) backdrop.removeAttribute('hidden');
      if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
      document.documentElement.style.overflow = 'hidden';
    }

    function closeSidebar() {
      body.classList.remove('sidebar-open');
      if (backdrop) backdrop.setAttribute('hidden', '');
      if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
      document.documentElement.style.overflow = '';
    }

    if (openBtn) {
      openBtn.addEventListener('click', function () {
        if (body.classList.contains('sidebar-open')) {
          closeSidebar();
        } else {
          openSidebar();
        }
      });
    }

    closeTargets.forEach(function (el) {
      el.addEventListener('click', closeSidebar);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
        closeSidebar();
      }
    });

    document.querySelectorAll('.sidebar-pages a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.matchMedia('(max-width: 900px)').matches) {
          closeSidebar();
        }
      });
    });

    window.addEventListener('resize', function () {
      if (window.matchMedia('(min-width: 901px)').matches) {
        closeSidebar();
      }
    });
  }

  function initActiveSidebar() {
    var path = window.location.pathname.replace(/\\/g, '/');
    var page = path.split('/').pop() || 'index.html';

    document.querySelectorAll('.sidebar-pages a').forEach(function (a) {
      var href = a.getAttribute('href') || '';
      if (href === page || href.endsWith('/' + page)) {
        a.classList.add('is-active');
      }
    });
  }

  function initTocSpy() {
    var links = document.querySelectorAll('.doc-toc-link[data-toc-target]');
    if (!links.length) return;

    var headings = [];
    links.forEach(function (link) {
      var id = link.getAttribute('data-toc-target');
      var el = document.getElementById(id);
      if (el) headings.push({ id: id, el: el, link: link });
    });
    if (!headings.length) return;

    function setActive(id) {
      links.forEach(function (l) {
        l.classList.toggle('is-active', l.getAttribute('data-toc-target') === id);
      });
    }

    if ('IntersectionObserver' in window) {
      var visible = new Map();
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            visible.set(entry.target.id, entry.intersectionRatio);
          });
          var best = null;
          var bestRatio = 0;
          headings.forEach(function (h) {
            var ratio = visible.get(h.id) || 0;
            if (ratio > bestRatio) {
              bestRatio = ratio;
              best = h.id;
            }
          });
          if (best) setActive(best);
        },
        { rootMargin: '-10% 0px -70% 0px', threshold: [0, 0.1, 0.5, 1] }
      );
      headings.forEach(function (h) {
        observer.observe(h.el);
      });
    }

    links.forEach(function (link) {
      link.addEventListener('click', function () {
        setActive(link.getAttribute('data-toc-target'));
      });
    });
  }

  initBack();
  initCopyMd();
  initSidebar();
  initActiveSidebar();
  initTocSpy();
})();
