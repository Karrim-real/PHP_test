document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "http://localhost/api";
  let authToken = localStorage.getItem("token");

  // Register User
  document
    .getElementById("register-form")
    .addEventListener("submit", async (e) => {
      e.preventDefault();
      const name = document.getElementById("reg-name").value;
      const email = document.getElementById("reg-email").value;
      const phone = document.getElementById("reg-phone").value;
      const password = document.getElementById("reg-password").value;

      const response = await fetch(`${apiUrl}/register`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, email, phone, password }),
      });

      const data = await response.json();
      alert(data.message || data.error);
    });

  // Login User
  document
    .getElementById("login-form")
    .addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = document.getElementById("login-email").value;
      const password = document.getElementById("login-password").value;

      const response = await fetch(`${apiUrl}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();
      if (data.token) {
        localStorage.setItem("token", data.token);
        window.location.href = "dashboard.html";
      } else {
        alert(data.error);
      }
    });

  // Fetch and Display Customers
  async function loadCustomers() {
    const response = await fetch(`${apiUrl}/customers`, {
      headers: { Authorization: `Bearer ${authToken}` },
    });
    const customers = await response.json();

    const tableBody = document.querySelector("#customer-table tbody");
    tableBody.innerHTML = ""; // Clear previous data

    customers.forEach((customer) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                <td>${customer.name}</td>
                <td>${customer.email}</td>
                <td>${customer.phone}</td>
                <td><a href="${customer.cv}" target="_blank">View CV</a></td>
                <td>
                    <button onclick="deleteCustomer(${customer.id})">Delete</button>
                </td>
            `;
      tableBody.appendChild(row);
    });
  }

  if (authToken && window.location.pathname.includes("dashboard.html")) {
    loadCustomers();
  }

  // Create Customer
  document
    .getElementById("customer-form")
    .addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData();
      formData.append("name", document.getElementById("customer-name").value);
      formData.append("email", document.getElementById("customer-email").value);
      formData.append("phone", document.getElementById("customer-phone").value);
      formData.append("cv", document.getElementById("customer-cv").files[0]);

      const response = await fetch(`${apiUrl}/customers`, {
        method: "POST",
        headers: { Authorization: `Bearer ${authToken}` },
        body: formData,
      });

      const data = await response.json();
      alert(data.message || data.error);
      loadCustomers();
    });

  // Delete Customer
  async function deleteCustomer(id) {
    const response = await fetch(`${apiUrl}/customers/${id}`, {
      method: "DELETE",
      headers: { Authorization: `Bearer ${authToken}` },
    });

    const data = await response.json();
    alert(data.message || data.error);
    loadCustomers();
  }
});
