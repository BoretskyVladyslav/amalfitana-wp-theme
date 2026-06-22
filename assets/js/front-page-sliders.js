(function () {
  'use strict';

function initCustomSlider(trackSelector, prevBtnSelector, nextBtnSelector, dotsContainerSelector, cardSelector, gap, options) {
  options = options || {};
  const centerActiveOption = options.centerActive;
  const minLoopCount = typeof options.minLoopCount === 'number' ? options.minLoopCount : 3;
  const dotClass = options.dotClass || 'tours-slider-pagination__dot';
  const track = document.querySelector(trackSelector);
  const prevBtn = prevBtnSelector ? document.querySelector(prevBtnSelector) : null;
  const nextBtn = nextBtnSelector ? document.querySelector(nextBtnSelector) : null;
  const paginationEl = dotsContainerSelector ? document.querySelector(dotsContainerSelector) : null;
  const sliderRoot = track ? (track.closest('.desktop-tours-wrapper') || track.closest('.tours-slider-container') || track.parentElement) : null;

  if (!track) {
    return;
  }

  track.style.animation = '';

  const DRAG_THRESHOLD = 80;
  let step = gap;

  const originals = Array.from(track.querySelectorAll(cardSelector));
  const originalCount = originals.length;

  if (originalCount === 0) {
    return;
  }

  const isLoop = originalCount >= minLoopCount;
  const VISIBLE_CLONES = isLoop ? 2 : 0;

  if (sliderRoot) {
    sliderRoot.classList.toggle('is-finite', !isLoop);
    sliderRoot.classList.toggle('is-single', originalCount === 1);
  }

  if (isLoop) {
    originals.slice(-VISIBLE_CLONES).reverse().forEach(function (card) {
      track.insertBefore(card.cloneNode(true), track.firstChild);
    });

    originals.slice(0, VISIBLE_CLONES).forEach(function (card) {
      track.appendChild(card.cloneNode(true));
    });
  } else if (prevBtn && nextBtn) {
    if (originalCount <= 1) {
      prevBtn.hidden = true;
      nextBtn.hidden = true;
    } else {
      prevBtn.hidden = false;
      nextBtn.hidden = false;
    }
  }

  let index = isLoop ? VISIBLE_CLONES : 0;
  let currentTranslate = 0;
  let isAnimating = false;
  let isDragging = false;
  let dragStartX = 0;
  let dragDelta = 0;

  function isCenterActive() {
    if (!isLoop && window.matchMedia('(min-width: 769px)').matches) {
      return true;
    }

    if (centerActiveOption === 'mobile') {
      return window.matchMedia('(max-width: 768px)').matches;
    }

    return !!centerActiveOption;
  }

  function getCenterOffset() {
    if (!isCenterActive()) {
      return 0;
    }

    const viewport = track.parentElement;
    const cards = track.querySelectorAll(cardSelector);
    const card = cards[index] || cards[0];

    if (!viewport || !card) {
      return 0;
    }

    return (viewport.offsetWidth - card.offsetWidth) / 2;
  }

  function updateStep() {
    const cards = track.querySelectorAll(cardSelector);
    const card = cards[index] || cards[0];

    if (!card) {
      return;
    }

    const computedGap = parseFloat(window.getComputedStyle(track).gap);
    step = card.offsetWidth + (Number.isFinite(computedGap) ? computedGap : gap);
    setTransform(getCenterOffset() - index * step, false);
  }

  function setTransform(value, animate) {
    if (animate) {
      track.classList.add('animate');
    } else {
      track.classList.remove('animate');
    }
    track.style.transform = 'translateX(' + value + 'px)';
    currentTranslate = value;
  }

  function getRealIndex() {
    if (!isLoop) {
      return Math.max(0, Math.min(originalCount - 1, index));
    }

    return ((index - VISIBLE_CLONES) % originalCount + originalCount) % originalCount;
  }

  function updatePagination() {
    if (!paginationEl) {
      return;
    }

    const activeIndex = getRealIndex();
    paginationEl.querySelectorAll('.' + dotClass).forEach(function (dot, dotIndex) {
      dot.classList.toggle('is-active', dotIndex === activeIndex);
      dot.setAttribute('aria-selected', dotIndex === activeIndex ? 'true' : 'false');
    });
  }

  function buildPagination() {
    if (!paginationEl || originalCount <= 1) {
      return;
    }

    paginationEl.innerHTML = '';

    for (let i = 0; i < originalCount; i += 1) {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = dotClass;
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
      dot.addEventListener('click', function () {
        goToSlide(i);
      });
      paginationEl.appendChild(dot);
    }

    updatePagination();
  }

  function normalizeIndex() {
    if (!isLoop) {
      if (index < 0) {
        index = 0;
      } else if (index > originalCount - 1) {
        index = originalCount - 1;
      }
      setTransform(getCenterOffset() - index * step, false);
      return;
    }

    if (index >= originalCount + VISIBLE_CLONES) {
      index -= originalCount;
      setTransform(getCenterOffset() - index * step, false);
    } else if (index < VISIBLE_CLONES) {
      index += originalCount;
      setTransform(getCenterOffset() - index * step, false);
    }
  }

  function slide(direction) {
    if (isAnimating || isDragging) {
      return;
    }

    if (!isLoop) {
      const nextIndex = index + (direction === 'next' ? 1 : -1);
      if (nextIndex < 0 || nextIndex > originalCount - 1) {
        return;
      }
      index = nextIndex;
    } else {
      index += direction === 'next' ? 1 : -1;
    }

    isAnimating = true;
    setTransform(getCenterOffset() - index * step, true);
  }

  function goToSlide(targetIndex) {
    if (isAnimating || isDragging) {
      return;
    }

    index = isLoop ? targetIndex + VISIBLE_CLONES : targetIndex;
    isAnimating = true;
    setTransform(getCenterOffset() - index * step, true);
  }

  track.addEventListener('transitionend', function (event) {
    if (event.propertyName !== 'transform') {
      return;
    }

    isAnimating = false;
    track.classList.remove('animate');
    normalizeIndex();
    updatePagination();
  });

  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      slide('prev');
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      slide('next');
    });
  }

  track.addEventListener('pointerdown', function (event) {
    if (isAnimating || (!isLoop && originalCount <= 1)) {
      return;
    }

    if (event.target.closest('a, button, input, textarea, select, label, [role="button"]')) {
      return;
    }

    event.preventDefault();
    isDragging = true;
    dragStartX = event.clientX;
    dragDelta = 0;
    track.classList.add('is-dragging');
    track.classList.remove('animate');
    track.setPointerCapture(event.pointerId);
  });

  track.addEventListener('pointermove', function (event) {
    if (!isDragging) {
      return;
    }

    dragDelta = event.clientX - dragStartX;
    track.style.transform = 'translateX(' + (currentTranslate + dragDelta) + 'px)';
  });

  function finishDrag() {
    if (!isDragging) {
      return;
    }

    isDragging = false;
    track.classList.remove('is-dragging');

    if (Math.abs(dragDelta) > DRAG_THRESHOLD) {
      slide(dragDelta < 0 ? 'next' : 'prev');
    } else {
      setTransform(currentTranslate, true);
    }

    dragDelta = 0;
  }

  track.addEventListener('pointerup', finishDrag);
  track.addEventListener('pointercancel', finishDrag);
  track.addEventListener('lostpointercapture', finishDrag);

  updateStep();
  buildPagination();

  window.addEventListener('resize', function () {
    updateStep();
  });

  window.addEventListener('load', function () {
    requestAnimationFrame(function () {
      updateStep();
    });
  });
}

var MOBILE_TOURS_WRAPPER_SELECTOR = '.mobile-tours-wrapper';
var MOBILE_TOURS_SWIPER_SELECTOR = '.mobile-tours-wrapper .swiper';

function isMobileToursVisible(wrapper) {
  if (!wrapper) {
    return false;
  }

  return wrapper.clientWidth > 0 && window.matchMedia('(max-width: 768px)').matches;
}

function syncMobileToursSwiper() {
  if (typeof Swiper === 'undefined') {
    return;
  }

  var wrapper = document.querySelector(MOBILE_TOURS_WRAPPER_SELECTOR);
  var container = document.querySelector(MOBILE_TOURS_SWIPER_SELECTOR);

  if (!wrapper || !container) {
    return;
  }

  var isVisible = isMobileToursVisible(wrapper);
  var slideCount = container.querySelectorAll('.swiper-slide').length;

  if (!isVisible) {
    if (container.swiper) {
      container.swiper.destroy(true, true);
    }

    return;
  }

  if (slideCount === 0) {
    return;
  }

  var enableLoop = slideCount >= 3;

  if (!container.swiper) {
    new Swiper(container, {
      slidesPerView: 'auto',
      spaceBetween: 16,
      loop: enableLoop,
      loopAdditionalSlides: enableLoop ? 2 : 0,
      grabCursor: true,
      watchOverflow: true,
    });
  } else {
    container.swiper.update();
  }
}

function startMobileToursSwiper() {
  var wrapper = document.querySelector(MOBILE_TOURS_WRAPPER_SELECTOR);

  if (!wrapper) {
    return;
  }

  syncMobileToursSwiper();

  if ('ResizeObserver' in window) {
    new ResizeObserver(syncMobileToursSwiper).observe(wrapper);
  }

  var mobileQuery = window.matchMedia('(max-width: 768px)');

  if (mobileQuery.addEventListener) {
    mobileQuery.addEventListener('change', syncMobileToursSwiper);
  } else if (mobileQuery.addListener) {
    mobileQuery.addListener(syncMobileToursSwiper);
  }

  window.addEventListener('resize', syncMobileToursSwiper);
  window.addEventListener('orientationchange', syncMobileToursSwiper);
}

function bootFrontPageSliders() {
  initCustomSlider('.desktop-tours-wrapper .custom-slider-track', '.desktop-tours-wrapper .tours-slider-prev', '.desktop-tours-wrapper .tours-slider-next', '.desktop-tours-wrapper .tours-slider-pagination', '.tour-card', 30, { centerActive: 'mobile', minLoopCount: 3, dotClass: 'tours-slider-pagination__dot' });
  initCustomSlider('.photos-slider-track', '.photos-prev', '.photos-next', '.photos-pagination', '.photo-slide:not([aria-hidden="true"])', 20, { centerActive: 'mobile', dotClass: 'photos-pagination__dot' });
  initCustomSlider('.people-slider-track', null, null, '.people-pagination', '.review-card', 30, { centerActive: 'mobile', dotClass: 'people-pagination__dot' });
  startMobileToursSwiper();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootFrontPageSliders);
} else {
  bootFrontPageSliders();
}

window.addEventListener('load', syncMobileToursSwiper);
})();
