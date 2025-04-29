const now = new Date();
const year = String(now.getFullYear());
const month = String(now.getMonth() + 1).padStart(2, 0);
const day = String(now.getDate()).padStart(2, 0);
const dateString = `${year}-${month}-${day}`;
document.getElementById('date').textContent = dateString;