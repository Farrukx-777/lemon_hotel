// main.js - Lemon Hotel umumiy interaktiv funksiyalari

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobil menyu ochish/yopish
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('show');
            const icon = menuToggle.querySelector('i');
            if (icon) {
                if (navLinks.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // 2. FAQ akkordeon mantiqi
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                faqItems.forEach(otherItem => otherItem.classList.remove('active'));
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        }
    });

    // 3. Sanalarni boshqarish (Bugungi kundan oldingi sanalarni tanlashni cheklash)
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    if (checkInInput && checkOutInput) {
        const today = new Date().toISOString().split('T')[0];
        checkInInput.min = today;

        // Ertangi kun
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        checkOutInput.min = tomorrow.toISOString().split('T')[0];

        // Kelish sanasi o'zgarganda ketish sanasini moslash
        checkInInput.addEventListener('change', function () {
            if (this.value) {
                const inDate = new Date(this.value);
                inDate.setDate(inDate.getDate() + 1);
                const nextDay = inDate.toISOString().split('T')[0];
                checkOutInput.min = nextDay;
                if (checkOutInput.value && checkOutInput.value <= this.value) {
                    checkOutInput.value = nextDay;
                }
            }
            calculateBookingTotal();
        });

        checkOutInput.addEventListener('change', calculateBookingTotal);
    }

    // 4. Bosh sahifadagi tezkor qidiruv formasi (Quick booking)
    const quickBookingForm = document.getElementById('quickBookingForm');
    if (quickBookingForm) {
        quickBookingForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const roomType = document.getElementById('quick_room_type')?.value || '';
            const checkIn = document.getElementById('quick_check_in')?.value || '';
            const checkOut = document.getElementById('quick_check_out')?.value || '';
            const guests = document.getElementById('quick_guests')?.value || '1';

            // Parametrlarni booking.html ga uzatamiz
            const params = new URLSearchParams({
                room: roomType,
                check_in: checkIn,
                check_out: checkOut,
                guests: guests
            });
            window.location.href = `booking.html?${params.toString()}`;
        });
    }

    // 5. Booking sahifasidagi parametrlarni URL'dan o'qib formaga qo'yish
    if (window.location.pathname.includes('booking.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const roomParam = urlParams.get('room');
        const checkInParam = urlParams.get('check_in');
        const checkOutParam = urlParams.get('check_out');
        const guestsParam = urlParams.get('guests');

        if (roomParam && document.getElementById('room_type')) {
            document.getElementById('room_type').value = roomParam;
        }
        if (checkInParam && document.getElementById('check_in')) {
            document.getElementById('check_in').value = checkInParam;
        }
        if (checkOutParam && document.getElementById('check_out')) {
            document.getElementById('check_out').value = checkOutParam;
        }
        if (guestsParam && document.getElementById('guests_count')) {
            document.getElementById('guests_count').value = guestsParam;
        }

        // Dastlabki narxni hisoblash
        setTimeout(calculateBookingTotal, 100);
    }

    // 6. Xona tanlanganda yoki mehmonlar o'zgarganda narxni yangilash
    const roomTypeSelect = document.getElementById('room_type');
    if (roomTypeSelect) {
        roomTypeSelect.addEventListener('change', calculateBookingTotal);
    }

    // 7. Bron qilish formasi (AJAX orqali backendga yuborish)
    const mainBookingForm = document.getElementById('mainBookingForm');
    if (mainBookingForm) {
        mainBookingForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBookingBtn');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Bron qilish';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Bron qilinmoqda...';
            }

            const formData = new FormData(this);

            fetch('save_booking.php', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('PHP server mavjud emas (Demo rejimi)');
                return res.json();
            })
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }

                if (data.status === 'success') {
                    showBookingSuccessModal(data.data);
                    mainBookingForm.reset();
                    calculateBookingTotal();
                } else {
                    alert(data.message || 'Xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.');
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
                
                // GitHub Pages yoki statik serverda ishlatilganda Demo ko'rinishida muvaffaqiyatli qabul qilish
                const checkInVal = document.getElementById('check_in')?.value || '';
                const checkOutVal = document.getElementById('check_out')?.value || '';
                const fullNameVal = document.getElementById('full_name')?.value || 'Hurmatli Mehmon';
                const selectedRoomVal = document.getElementById('room_type')?.value || 'standard';
                const roomInfo = ROOM_PRICES[selectedRoomVal] || { price: 380000, name: 'Standart Xona' };

                let nights = 1;
                if (checkInVal && checkOutVal) {
                    const inDate = new Date(checkInVal);
                    const outDate = new Date(checkOutVal);
                    const diffTime = outDate - inDate;
                    if (diffTime > 0) nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                }
                const total = nights * roomInfo.price;
                const randomCode = 'LH-' + Math.floor(10000 + Math.random() * 90000);

                showBookingSuccessModal({
                    booking_code: randomCode + ' (Demo)',
                    full_name: fullNameVal,
                    room_name: roomInfo.name,
                    check_in: checkInVal || 'Bugun',
                    check_out: checkOutVal || 'Ertaga',
                    nights: nights,
                    total_price: formatMoney(total) + ' so\'m'
                });
                mainBookingForm.reset();
                calculateBookingTotal();
            });
        });
    }

    // 8. Aloqa formasi (AJAX)
    const contactForm = document.getElementById('mainContactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitContactBtn');
            const originalText = submitBtn ? submitBtn.innerHTML : 'Yuborish';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Yuborilmoqda...';
            }

            const formData = new FormData(this);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('PHP server mavjud emas');
                return res.json();
            })
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }

                const alertBox = document.getElementById('contactAlert');
                if (alertBox) {
                    alertBox.style.display = 'block';
                    if (data.status === 'success') {
                        alertBox.style.background = '#dcfce7';
                        alertBox.style.color = '#15803d';
                        alertBox.style.border = '1px solid #bbf7d0';
                        alertBox.innerHTML = '<i class="fa-solid fa-check-circle"></i> ' + data.message;
                        contactForm.reset();
                    } else {
                        alertBox.style.background = '#fee2e2';
                        alertBox.style.color = '#b91c1c';
                        alertBox.style.border = '1px solid #fecaca';
                        alertBox.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + data.message;
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
                const alertBox = document.getElementById('contactAlert');
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#dcfce7';
                    alertBox.style.color = '#15803d';
                    alertBox.style.border = '1px solid #bbf7d0';
                    alertBox.innerHTML = '<i class="fa-solid fa-check-circle"></i> Xabaringiz qabul qilindi! Tez orada mutaxassisimiz siz bilan bog\'lanadi.';
                    contactForm.reset();
                } else {
                    alert('Xabaringiz muvaffaqiyatli qabul qilindi!');
                }
            });
        });
    }
});

// Xona narxini hisoblash funksiyasi
const ROOM_PRICES = {
    standard: { price: 380000, name: 'Standart Qulay Xona' },
    deluxe: { price: 580000, name: 'Deluxe Premium Xona' },
    suite: { price: 890000, name: 'Luxe Biznes Suite' },
    family: { price: 1400000, name: 'Prezident Oilaviy Suite' }
};

function calculateBookingTotal() {
    const roomSelect = document.getElementById('room_type');
    const checkIn = document.getElementById('check_in')?.value;
    const checkOut = document.getElementById('check_out')?.value;

    const summaryRoom = document.getElementById('summary_room_name');
    const summaryNights = document.getElementById('summary_nights');
    const summaryPricePerNight = document.getElementById('summary_price_per_night');
    const summaryTotal = document.getElementById('summary_total_price');

    if (!roomSelect) return;

    const selectedSlug = roomSelect.value;
    const roomInfo = ROOM_PRICES[selectedSlug] || { price: 380000, name: 'Xona tanlanmagan' };

    let nights = 1;
    if (checkIn && checkOut) {
        const inDate = new Date(checkIn);
        const outDate = new Date(checkOut);
        const diffTime = outDate - inDate;
        if (diffTime > 0) {
            nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        }
    }

    const total = nights * roomInfo.price;

    if (summaryRoom) summaryRoom.textContent = roomInfo.name;
    if (summaryNights) summaryNights.textContent = `${nights} kecha`;
    if (summaryPricePerNight) summaryPricePerNight.textContent = `${formatMoney(roomInfo.price)} so'm`;
    if (summaryTotal) summaryTotal.textContent = `${formatMoney(total)} so'm`;
}

function formatMoney(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

// Muvaffaqiyat modali
function showBookingSuccessModal(data) {
    const modal = document.getElementById('bookingSuccessModal');
    if (!modal) return;

    document.getElementById('modalBookingCode').textContent = data.booking_code;
    document.getElementById('modalClientName').textContent = data.full_name;
    document.getElementById('modalRoomName').textContent = data.room_name;
    document.getElementById('modalDates').textContent = `${data.check_in} — ${data.check_out} (${data.nights} kecha)`;
    document.getElementById('modalTotalPrice').textContent = data.total_price;

    modal.style.display = 'flex';
}

function closeBookingModal() {
    const modal = document.getElementById('bookingSuccessModal');
    if (modal) {
        modal.style.display = 'none';
    }
}
