function calculateAge() {
    const birthdayInput = document.getElementById('birthday');
    const ageValueSpan = document.getElementById('ageValue');

    const birthday = new Date(birthdayInput.value);
    if (isNaN(birthday.getTime())) {
        ageValueSpan.textContent = 'N/A';
        return;
    }

    const today = new Date();
    let age = today.getFullYear() - birthday.getFullYear();
    const monthDiff = today.getMonth() - birthday.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
        age--;
    }

    ageValueSpan.textContent = age;
}

function togglePassword(id) {
    const passwordInput = document.getElementById(id);
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
}