// Section: Nav Toggle
document.addEventListener("DOMContentLoaded", function () {
    var body = document.body;

    var openSearchBtns = document.querySelectorAll('[data-open="search"]');
    var closeSearchBtn = document.querySelector('[data-close="search"]');
    var searchDrop = document.querySelector("[data-search]");

    var toggleMobileBtn = document.querySelector('[data-toggle="mobile"]');
    var closeMobileBtn = document.querySelector('[data-close="mobile"]');
    var overlay = document.querySelector("[data-overlay]");

    function openSearch() {
        body.classList.add("is-open-search");
    }
    function closeSearch() {
        body.classList.remove("is-open-search");
    }
    function openMobile() {
        body.classList.add("is-open-mobile");
    }
    function closeMobile() {
        body.classList.remove("is-open-mobile");
    }

    openSearchBtns.forEach((btn) => btn.addEventListener("click", openSearch));
    if (closeSearchBtn) {
        closeSearchBtn.addEventListener("click", closeSearch);
    }
    if (toggleMobileBtn) {
        toggleMobileBtn.addEventListener("click", openMobile);
    }
    if (closeMobileBtn) {
        closeMobileBtn.addEventListener("click", closeMobile);
    }
    if (overlay) {
        overlay.addEventListener("click", function () {
            closeSearch();
            closeMobile();
        });
    }

    document.addEventListener("keyup", function (e) {
        if (e.key === "Escape") {
            closeSearch();
            closeMobile();
        }
    });
});

// Hero Slider
(function () {
    var slider = document.querySelector("[data-slider]");
    if (!slider) return;
    var slides = Array.from(slider.querySelectorAll(".hero-slide"));
    var dotsWrap = slider.querySelector("[data-dots]");
    var idx = 0,
        t = null,
        dur = 6000;
    function go(i) {
        slides[idx].classList.remove("is-active");
        idx = (i + slides.length) % slides.length;
        slides[idx].classList.add("is-active");
        updateDots();
    }
    function updateDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = "";
        slides.forEach(function (_, i) {
            var b = document.createElement("button");
            if (i === idx) b.classList.add("is-active");
            b.addEventListener("click", function () {
                stop();
                go(i);
                start();
            });
            dotsWrap.appendChild(b);
        });
    }
    function start() {
        stop();
        t = setInterval(function () {
            go(idx + 1);
        }, dur);
    }
    function stop() {
        if (t) clearInterval(t);
        t = null;
    }
    slides[0].classList.add("is-active");
    updateDots();
    start();
})();

// Featured Products Carousel - FINAL FIX
(function () {
    var wrap = document.querySelector("[data-featured]");
    if (!wrap) return;

    var track = wrap.querySelector("[data-track]");
    var prevBtn = wrap.querySelector("[data-prev]");
    var nextBtn = wrap.querySelector("[data-next]");

    if (!track || !prevBtn || !nextBtn) return;

    // Wait for track to have children
    setTimeout(function () {
        var originalCards = Array.from(track.children);

        if (originalCards.length === 0) return;

        var currentIndex = 0;
        var timer = null;
        var dur = 4000;
        var isTransitioning = false;
        var isRTL = document.documentElement.dir === "rtl";

        // Clone cards for infinite loop
        for (let i = 0; i < 2; i++) {
            originalCards.forEach(function (card) {
                var clone = card.cloneNode(true);
                track.appendChild(clone);
            });
        }

        var totalOriginal = originalCards.length;

        function getCardWidth() {
            if (originalCards[0]) {
                return originalCards[0].offsetWidth;
            }
            return 0;
        }

        function getGap() {
            return window.innerWidth <= 680 ? 16 : 22;
        }

        function getSlideWidth() {
            return getCardWidth() + getGap();
        }

        function slideTo(index, instant) {
            var offset = index * getSlideWidth();

            if (instant) {
                track.style.transition = "none";
            } else {
                track.style.transition = "transform 0.45s ease";
            }

            // Use negative translateX for both LTR and RTL
            var direction = isRTL ? 1 : -1;
            track.style.transform = "translateX(" + direction * offset + "px)";

            if (instant) {
                setTimeout(function () {
                    track.style.transition = "";
                }, 50);
            }
        }

        function handleTransitionEnd(e) {
            // Only handle transform transitions on the track
            if (e.target !== track || e.propertyName !== "transform") return;

            if (currentIndex >= totalOriginal) {
                currentIndex = currentIndex - totalOriginal;
                slideTo(currentIndex, true);
            } else if (currentIndex < 0) {
                currentIndex = totalOriginal + currentIndex;
                slideTo(currentIndex, true);
            }
            isTransitioning = false;
        }

        function goNext() {
            if (isTransitioning) return;
            isTransitioning = true;
            currentIndex++;
            slideTo(currentIndex, false);
        }

        function goPrev() {
            if (isTransitioning) return;
            isTransitioning = true;
            currentIndex--;
            slideTo(currentIndex, false);
        }

        function startAutoplay() {
            stopAutoplay();
            timer = setInterval(function () {
                goNext();
            }, dur);
        }

        function stopAutoplay() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        // Event listeners
        track.addEventListener("transitionend", handleTransitionEnd);

        prevBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            stopAutoplay();
            goPrev();
            startAutoplay();
        });

        nextBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            stopAutoplay();
            goNext();
            startAutoplay();
        });

        wrap.addEventListener("mouseenter", function () {
            stopAutoplay();
        });

        wrap.addEventListener("mouseleave", function () {
            startAutoplay();
        });

        var resizeTimer;
        window.addEventListener("resize", function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                slideTo(currentIndex, true);
            }, 250);
        });

        // Initialize - CRITICAL: Set position first, then start autoplay
        slideTo(0, true);

        // Start autoplay after a small delay to ensure everything is ready
        setTimeout(function () {
            startAutoplay();
        }, 100);
    }, 100); // Wait 100ms for DOM to be fully ready
})();

// Brands Carousel
(function () {
    var root = document.querySelector("[data-brands]");
    if (!root) return;

    var track = root.querySelector("[data-track]");
    var prevBtn = root.querySelector("[data-prev]");
    var nextBtn = root.querySelector("[data-next]");

    if (!track || !prevBtn || !nextBtn) return;

    setTimeout(function () {
        var originalItems = Array.from(track.children);

        if (originalItems.length === 0) return;

        var currentIndex = 0;
        var timer = null;
        var dur = 3000;
        var isTransitioning = false;

        // Clone items for infinite loop
        for (let i = 0; i < 2; i++) {
            originalItems.forEach(function (item) {
                var clone = item.cloneNode(true);
                track.appendChild(clone);
            });
        }

        var totalOriginal = originalItems.length;

        function getItemWidth() {
            if (originalItems[0]) {
                return originalItems[0].offsetWidth;
            }
            return 0;
        }

        function getGap() {
            return window.innerWidth <= 680 ? 16 : 42;
        }

        function getSlideWidth() {
            return getItemWidth() + getGap();
        }

        function slideTo(index, instant) {
            var offset = index * getSlideWidth();

            if (instant) {
                track.style.transition = "none";
            } else {
                track.style.transition = "transform 0.5s ease-in-out";
            }

            track.style.transform = "translateX(-" + offset + "px)";

            if (instant) {
                setTimeout(function () {
                    track.style.transition = "";
                }, 50);
            }
        }

        function handleTransitionEnd(e) {
            if (e.target !== track || e.propertyName !== "transform") return;

            if (currentIndex >= totalOriginal) {
                currentIndex = currentIndex - totalOriginal;
                slideTo(currentIndex, true);
            } else if (currentIndex < 0) {
                currentIndex = totalOriginal + currentIndex;
                slideTo(currentIndex, true);
            }
            isTransitioning = false;
        }

        function goNext() {
            if (isTransitioning) return;
            isTransitioning = true;
            currentIndex++;
            slideTo(currentIndex, false);
        }

        function goPrev() {
            if (isTransitioning) return;
            isTransitioning = true;
            currentIndex--;
            slideTo(currentIndex, false);
        }

        function startAutoplay() {
            stopAutoplay();
            timer = setInterval(function () {
                goNext();
            }, dur);
        }

        function stopAutoplay() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        track.addEventListener("transitionend", handleTransitionEnd);

        prevBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            stopAutoplay();
            goPrev();
            startAutoplay();
        });

        nextBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            stopAutoplay();
            goNext();
            startAutoplay();
        });

        root.addEventListener("mouseenter", function () {
            stopAutoplay();
        });

        root.addEventListener("mouseleave", function () {
            startAutoplay();
        });

        var resizeTimer;
        window.addEventListener("resize", function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                slideTo(currentIndex, true);
            }, 250);
        });

        slideTo(0, true);
        setTimeout(function () {
            startAutoplay();
        }, 100);
    }, 100);
})();

// About Tabs
(function () {
    var tabs = document.querySelectorAll(".about-tab");
    if (!tabs.length) return;
    var panels = document.querySelectorAll(".about-panel");
    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            var target = tab.getAttribute("data-tab");
            tabs.forEach(function (t) {
                t.classList.remove("is-active");
            });
            panels.forEach(function (p) {
                p.classList.remove("is-active");
            });
            tab.classList.add("is-active");
            var panel = document.querySelector(
                '.about-panel[data-panel="' + target + '"]'
            );
            if (panel) panel.classList.add("is-active");
        });
    });
})();

// News Filtering
(function () {
    var list = document.querySelector("[data-news-list]");
    if (!list) return;
    var items = [].slice.call(list.querySelectorAll(".news-item"));
    var tabs = document.querySelectorAll("[data-news-tabs] .news-tab");
    var search = document.querySelector("[data-news-search]");
    var more = document.querySelector("[data-news-more]");
    var visibleCount = 3;

    function applyFilters() {
        var activeYear = document
            .querySelector(".news-tab.is-active")
            .getAttribute("data-year");
        var q = ((search && search.value) || "").toLowerCase().trim();
        var shown = 0;
        items.forEach(function (item) {
            var year = item.getAttribute("data-year");
            var text = item.textContent.toLowerCase();
            var matchYear = activeYear === "all" || String(year) === activeYear;
            var matchText = !q || text.indexOf(q) !== -1;
            if (matchYear && matchText && shown < visibleCount) {
                item.classList.remove("is-hidden");
                shown++;
            } else if (matchYear && matchText) {
                item.classList.add("is-hidden");
            } else {
                item.classList.add("is-hidden");
            }
        });
        var remaining = items.filter(function (item) {
            return !item.classList.contains("is-hidden");
        }).length;
        var totalMatch = items.filter(function (item) {
            var year = item.getAttribute("data-year");
            var text = item.textContent.toLowerCase();
            var matchYear = activeYear === "all" || String(year) === activeYear;
            var matchText = !q || text.indexOf(q) !== -1;
            return matchYear && matchText;
        }).length;
        if (more) {
            if (remaining >= totalMatch || totalMatch <= visibleCount) {
                more.style.display = "none";
            } else {
                more.style.display = "inline-flex";
            }
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            tabs.forEach(function (t) {
                t.classList.remove("is-active");
            });
            tab.classList.add("is-active");
            visibleCount = 3;
            applyFilters();
        });
    });

    if (search) {
        search.addEventListener("input", function () {
            visibleCount = 3;
            applyFilters();
        });
    }

    if (more) {
        more.addEventListener("click", function () {
            visibleCount += 3;
            applyFilters();
        });
    }

    applyFilters();
})();

// Contact Form Tabs
(function () {
    var roots = document.querySelectorAll("[data-contact]");
    if (!roots.length) return;
    roots.forEach(function (root) {
        var tabs = [].slice.call(root.querySelectorAll(".contact-tab"));
        var extraRow = root.querySelector('[data-extra="product"]');
        var btn = root.querySelector("[data-contact-btn]");
        var btnText = btn ? btn.querySelector(".contact-submit-text") : null;

        function applyTab(tab) {
            tabs.forEach(function (t) {
                t.classList.remove("is-active");
            });
            tab.classList.add("is-active");
            var showProduct = tab.getAttribute("data-product") === "1";
            var label = tab.getAttribute("data-btn") || "";
            if (extraRow) {
                if (showProduct) extraRow.classList.add("is-visible");
                else extraRow.classList.remove("is-visible");
            }
            if (btnText) btnText.textContent = label;
        }

        tabs.forEach(function (tab) {
            tab.addEventListener("click", function () {
                applyTab(tab);
            });
        });

        if (tabs[0]) applyTab(tabs[0]);
    });
})();

// Service Panel Switching
(function () {
    var root = document.querySelector(".service-shell__inner");
    if (!root) return;
    var btns = [].slice.call(root.querySelectorAll(".service-side-btn"));
    var panels = [].slice.call(root.querySelectorAll(".service-panel"));
    var pill = root.querySelector("[data-svc-pill]");
    var crumb = root.querySelector("[data-svc-breadcrumb] .svc-current");
    var title = root.querySelector("[data-svc-title]");
    var sub = root.querySelector("[data-svc-sub]");

    var headerData = {
        software: {
            pill: "Software&Our service",
            title: "Our Services",
            sub: "Comprehensive support throughout the entire equipment lifecycle",
        },
        appointment: {
            pill: "Book an appointment",
            title: "Book an appointment",
            sub: "Meet with our specialists to review your needs and plan the right solution",
        },
        parts: {
            pill: "Reqesr for parts",
            title: "Spare Parts Request",
            sub: "Fast processing of OEM spare parts orders for your equipment",
        },
    };

    function activate(btn) {
        var key = btn.getAttribute("data-panel");
        btns.forEach(function (b) {
            b.classList.remove("is-primary");
        });
        panels.forEach(function (p) {
            p.classList.remove("is-active");
        });
        btn.classList.add("is-primary");
        var panel = root.querySelector(
            '.service-panel[data-panel="' + key + '"]'
        );
        if (panel) panel.classList.add("is-active");
        var data = headerData[key];
        if (data) {
            if (pill) pill.textContent = data.pill;
            if (crumb) crumb.textContent = data.pill;
            if (title) title.textContent = data.title;
            if (sub) sub.textContent = data.sub;
        }
    }

    btns.forEach(function (btn) {
        btn.addEventListener("click", function () {
            activate(btn);
        });
    });

    if (btns[0]) activate(btns[0]);
})();

// Consumables Navigation
(function () {
    var root = document.querySelector(".cons-shell__inner");
    if (!root) return;
    var navs = [].slice.call(root.querySelectorAll("[data-cons-btn]"));
    var panels = [].slice.call(root.querySelectorAll(".cons-panel"));
    var crumb = root.querySelector("[data-cons-current]");
    var heading = root.querySelector("[data-cons-heading]");

    function activate(btn) {
        var key = btn.getAttribute("data-panel");
        var title = btn.getAttribute("data-title") || "";
        navs.forEach(function (n) {
            n.classList.remove("is-active");
        });
        btn.classList.add("is-active");
        panels.forEach(function (p) {
            if (p.getAttribute("data-panel") === key)
                p.classList.add("is-active");
            else p.classList.remove("is-active");
        });
        if (title) {
            if (crumb) crumb.textContent = title;
            if (heading) heading.textContent = title;
        }
    }

    navs.forEach(function (btn) {
        btn.addEventListener("click", function () {
            activate(btn);
        });
    });

    if (navs[0]) activate(navs[0]);
})();

// Consumables Sidebar Toggle
(function () {
    var top = document.querySelector(".cons-side-top");
    var box = document.querySelector(".cons-side-box");
    if (!top || !box) return;
    top.addEventListener("click", function () {
        var collapsed = box.classList.toggle("is-collapsed");
        top.classList.toggle("is-collapsed", collapsed);
    });
})();

// Products Brand Navigation
(function () {
    var root = document.querySelector(".products-shell__inner");
    if (!root) return;
    var navs = [].slice.call(root.querySelectorAll("[data-prod-brand]"));
    var panels = [].slice.call(root.querySelectorAll(".prod-panel"));
    var crumb = root.querySelector("[data-prod-current]");
    var heading = root.querySelector("[data-prod-heading]");

    function activate(btn) {
        var key = btn.getAttribute("data-panel");
        var title = btn.getAttribute("data-title") || "";
        navs.forEach(function (n) {
            n.classList.remove("is-active");
        });
        btn.classList.add("is-active");
        panels.forEach(function (p) {
            if (p.getAttribute("data-panel") === key)
                p.classList.add("is-active");
            else p.classList.remove("is-active");
        });
        if (title) {
            if (crumb) crumb.textContent = title;
            if (heading) heading.textContent = title;
        }
    }

    navs.forEach(function (btn) {
        btn.addEventListener("click", function () {
            activate(btn);
        });
    });

    if (navs[0]) activate(navs[0]);
})();

// Product Detail Tabs
(function () {
    var root = document.querySelector("[data-pdetail-tabs]");
    if (!root) return;
    var tabs = [].slice.call(root.querySelectorAll(".pdetail-tab"));
    var panels = [].slice.call(document.querySelectorAll(".pdetail-panel"));
    function activate(tab) {
        var key = tab.getAttribute("data-tab");
        tabs.forEach(function (t) {
            t.classList.remove("is-active");
        });
        tab.classList.add("is-active");
        panels.forEach(function (p) {
            if (p.getAttribute("data-panel") === key)
                p.classList.add("is-active");
            else p.classList.remove("is-active");
        });
    }
    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            activate(tab);
        });
    });
    if (tabs[0]) activate(tabs[0]);
})();
