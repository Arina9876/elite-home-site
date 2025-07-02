// ==== фиксируем имя пользователя в личном кабинете ====
const profileNameEl   = document.querySelector('.profile-card h3');
const FIXED_USER_NAME = profileNameEl ? profileNameEl.textContent : '';

const keepName = () => { if (profileNameEl) profileNameEl.textContent = FIXED_USER_NAME; };
// сразу вызовем
keepName();
// и каждые 100 мс проверяем, не переписал ли кто‑то имя
setInterval(keepName, 100);
// =======================================================
// 1) Переключение светлой/тёмной темы
const themeBtn = document.getElementById('theme-toggle');
themeBtn.addEventListener('click', () => {
  const root = document.documentElement;
  const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
  root.setAttribute('data-theme', next);
  themeBtn.textContent = next === 'light' ? '🌙' : '☀️';
});

// 2) Данные по объектам
const objectsData = {
  'villa-barvikha': {
    title: 'Особняк в Барвихе',
    desc: 'Этот невероятный особняк площадью 700 м² расположен в живописной долине Барвихи. ...',
    shortDesc: '700 м², 5 спален, бассейн, SPA-зона.',
    img: 'images/villa-barvikha.jpg'
  },
  'cottage-zhukovka': {
    title: 'Коттедж класса люкс в Жуковке',
    desc: 'Уютный особняк площадью 500 м² в элегантном стиле прованс: ...',
    shortDesc: '500 м², 3 спальни, камин, «умный дом».',
    img: 'images/cottage-zhukovka.jpg'
  },
  'apartments-country': {
    title: 'Апартаменты за городом',
    desc: 'Современный комплекс апартаментов общей площадью 1000 м² с видом на лес и реку. ...',
    shortDesc: '1000 м², лофт, вид на лес.',
    img: 'images/apartments-country.jpg'
  }
};

document.addEventListener('DOMContentLoaded', () => {
  //
  // A) Обработка одиночной карточки (если используете object-detail.html?object=…)
  //
  const params = new URLSearchParams(window.location.search);
  const key = params.get('object');
  if (key && objectsData[key]) {
    // заполняем заголовок/описание/картинку
    document.getElementById('obj-title').textContent = objectsData[key].title;
    document.getElementById('obj-desc').textContent = objectsData[key].desc;
    const imgEl = document.getElementById('obj-img');
    imgEl.src = objectsData[key].img;
    imgEl.alt = objectsData[key].title;

    // кнопка «Забронировать» для одиночной карточки
    const singleBtn = document.getElementById('book-btn');
    if (singleBtn) {
      singleBtn.addEventListener('click', () => {
        const bookings = JSON.parse(localStorage.getItem('bookings') || '[]');
        if (!bookings.includes(key)) {
          bookings.push(key);
          localStorage.setItem('bookings', JSON.stringify(bookings));
          alert('Объект забронирован!');
        } else {
          alert('Этот объект уже в ваших бронированиях.');
        }
        renderBookings(); // обновляем ЛК, если открыт
      });
    }
  }

  //
  // 😎 Обработка расширенных карточек на одной странице (class="book-detail")
  //
  document.querySelectorAll('.book-detail').forEach(btn => {
    btn.addEventListener('click', () => {
      const k = btn.dataset.key;
      const bookings = JSON.parse(localStorage.getItem('bookings') || '[]');
      if (!bookings.includes(k)) {
        bookings.push(k);
        localStorage.setItem('bookings', JSON.stringify(bookings));
        alert('Объект забронирован!');
      } else {
        alert('Этот объект уже в ваших бронированиях.');
      }
      renderBookings(); // обновляем ЛК, если открыт
    });
  });

  //
  // C) Личный кабинет
  //
  const showBookingBtn    = document.getElementById('show-booking');
  const bookingContainer  = document.getElementById('booking-container');
  const showSettingsBtn   = document.getElementById('show-settings');
  const settingsContainer = document.getElementById('settings-container');
  const logoutBtn         = document.getElementById('logout-btn');
  const settingsMsg       = document.getElementById('settings-message');

  // Функция рендера активных броней
  function renderBookings() {
    const listEl = document.getElementById('bookings-list');
    if (!listEl) return;
    listEl.innerHTML = '';

    const bookings = JSON.parse(localStorage.getItem('bookings') || '[]');
    if (bookings.length === 0) {
      listEl.innerHTML = '<p>У вас пока нет активных броней.</p>';
      return;
    }
    bookings.forEach(k => {
      const data = objectsData[k];
      if (!data) return;
      const card = document.createElement('div');
      card.className = 'booking-card';
      card.innerHTML = `
        <h3>${data.title}</h3>
        <p>${data.shortDesc}</p>
        <img src="${data.img}" alt="${data.title}">
        <button class="btn remove-booking" data-key="${k}">Убрать бронь</button>
      `;
      listEl.append(card);
    });

    // Навешиваем удаление
    document.querySelectorAll('.remove-booking').forEach(btn => {
      btn.addEventListener('click', () => {
        const keyToRemove = btn.dataset.key;
        let arr = JSON.parse(localStorage.getItem('bookings') || '[]');
        arr = arr.filter(item => item !== keyToRemove);
        localStorage.setItem('bookings', JSON.stringify(arr));
        renderBookings();
      });
    });
  }

  // Показ/скрытие списка броней
  if (showBookingBtn) {
    showBookingBtn.addEventListener('click', () => {
      settingsContainer.classList.add('hidden');
      bookingContainer.classList.toggle('hidden');
      showBookingBtn.textContent = bookingContainer.classList.contains('hidden')
        ? 'Активные брони'
        : 'Скрыть бронь';
      renderBookings();
    });
  }

  // Показ/скрытие настроек
  if (showSettingsBtn) {
    showSettingsBtn.addEventListener('click', () => {
      bookingContainer.classList.add('hidden');
      settingsContainer.classList.toggle('hidden');
      showSettingsBtn.textContent = settingsContainer.classList.contains('hidden')
        ? 'Настройки'
        : 'Закрыть настройки';
      showBookingBtn.textContent = 'Активные брони';
    });
  }

  // Выход из аккаунта
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      settingsMsg.textContent = 'Вы вышли из системы.';
    });
  }

  // Отрисовать сразу при загрузке ЛК
  renderBookings();
});
// 3) Бургер-меню
const burger = document.getElementById('burger');
const nav     = document.querySelector('nav');

burger.addEventListener('click', () => nav.classList.toggle('open'));