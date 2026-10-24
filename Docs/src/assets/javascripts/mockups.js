(function () {
  "use strict";

  function init() {
    var images = Array.prototype.slice.call(document.querySelectorAll(".mockup-grid img"));
    if (!images.length || images[0].dataset.mockupReady === "1") {
      return;
    }

    var overlay = null;
    var lastFocus = null;
    var keyHandler = null;

    function close() {
      if (!overlay) {
        return;
      }
      overlay.remove();
      overlay = null;
      document.body.classList.remove("mockup-lightbox-open");
      if (keyHandler) {
        document.removeEventListener("keydown", keyHandler);
        keyHandler = null;
      }
      if (lastFocus) {
        lastFocus.focus();
        lastFocus = null;
      }
    }

    function open(img) {
      lastFocus = img;

      overlay = document.createElement("div");
      overlay.className = "mockup-lightbox";
      overlay.setAttribute("role", "dialog");
      overlay.setAttribute("aria-modal", "true");
      overlay.setAttribute("aria-label", img.alt || "Imagem ampliada");

      var closeButton = document.createElement("button");
      closeButton.type = "button";
      closeButton.className = "mockup-lightbox-close";
      closeButton.setAttribute("aria-label", "Fechar imagem");
      closeButton.textContent = "\u00d7";

      var large = document.createElement("img");
      large.className = "mockup-lightbox-image";
      large.src = img.currentSrc || img.src;
      large.alt = img.alt || "";

      var hint = document.createElement("p");
      hint.className = "mockup-lightbox-hint";
      hint.textContent = "Esc para fechar";

      var canZoom = false;
      var zoomed = false;

      function updateHint() {
        if (zoomed) {
          hint.textContent = "Clique para ajustar ao ecr\u00e3 \u00b7 Esc para fechar";
        } else if (canZoom) {
          hint.textContent = "Clique na imagem para ampliar \u00b7 Esc para fechar";
        } else {
          hint.textContent = "Esc para fechar";
        }
      }

      function measure() {
        var rendered = large.getBoundingClientRect().width;
        canZoom = large.naturalWidth > rendered + 8;
        if (canZoom) {
          large.setAttribute("tabindex", "0");
          large.setAttribute("role", "button");
          large.setAttribute("aria-label", "Ampliar imagem");
        }
        large.style.cursor = canZoom ? "zoom-in" : "default";
        updateHint();
      }

      function toggleZoom() {
        if (!canZoom) {
          return;
        }
        zoomed = !zoomed;
        large.classList.toggle("zoomed", zoomed);
        overlay.classList.toggle("is-zoomed", zoomed);
        large.style.cursor = zoomed ? "zoom-out" : "zoom-in";
        large.setAttribute("aria-label", zoomed ? "Ajustar imagem ao ecr\u00e3" : "Ampliar imagem");
        updateHint();
      }

      overlay.appendChild(closeButton);
      overlay.appendChild(large);
      overlay.appendChild(hint);
      document.body.appendChild(overlay);
      document.body.classList.add("mockup-lightbox-open");
      closeButton.focus();

      if (large.complete) {
        measure();
      } else {
        large.addEventListener("load", measure);
      }

      closeButton.addEventListener("click", close);
      overlay.addEventListener("click", function (event) {
        if (event.target === overlay) {
          close();
        }
      });
      large.addEventListener("click", function (event) {
        event.stopPropagation();
        toggleZoom();
      });
      large.addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          toggleZoom();
        }
      });

      keyHandler = function (event) {
        if (event.key === "Escape") {
          close();
        }
      };
      document.addEventListener("keydown", keyHandler);
    }

    images.forEach(function (img) {
      img.dataset.mockupReady = "1";
      img.classList.add("mockup-zoomable");
      img.setAttribute("tabindex", "0");
      img.setAttribute("role", "button");
      img.setAttribute("aria-label", (img.alt || "Imagem") + " \u2014 clicar para ampliar");

      img.addEventListener("click", function () {
        open(img);
      });

      img.addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          open(img);
        }
      });
    });
  }

  if (window.document$ && typeof window.document$.subscribe === "function") {
    window.document$.subscribe(init);
  } else if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
