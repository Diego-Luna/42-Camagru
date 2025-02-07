function validatePasswords() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchDiv = document.getElementById('password_match');
    
    if (newPassword !== confirmPassword) {
        matchDiv.textContent = 'Passwords do not match!';
        return false;
    }
    matchDiv.textContent = '';
    return true;
}

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    if (!validatePasswords()) {
        e.preventDefault();
    }
});

document.getElementById('confirm_password').addEventListener('input', validatePasswords);