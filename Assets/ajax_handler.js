/**
 * MedVerify AJAX Handler
 * Centralized AJAX functionality for all forms
 */

class AjaxHandler {
    constructor() {
        this.init();
    }

    init() {
        this.setupLoginForm();
        this.setupSignupForm();
        this.setupMedicineVerificationForm();
        this.setupMedicineManagementForms();
        this.setupManufacturerForms();
        this.setupFamilyForms();
        this.setupAppointmentForms();
        this.setupReportForms();
        this.setupCounterfeitForms();
    }

    setupLoginForm() {
        const form = document.querySelector('form[action*="loginCheck"]');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit(form, '../Controllers/loginCheck_ajax.php', (data) => {
                this.showNotification('success', data.message);
                setTimeout(() => window.location.href = data.data.redirect, 500);
            });
        });
    }

    setupSignupForm() {
        const form = document.querySelector('form[action*="signupCheck"]');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit(form, '../Controllers/signupCheck_ajax.php', (data) => {
                this.showNotification('success', data.message);
                setTimeout(() => window.location.href = '../Views/login.php', 1500);
            });
        });
    }

    setupMedicineVerificationForm() {
        const form = document.querySelector('form[action*="verify_medicine"]');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit(form, '../Controllers/verify_medicine_ai_ajax.php', (data) => {
                this.displayVerificationResults(data.data);
            }, true); // true for file upload
        });
    }

    setupMedicineManagementForms() {
        // Add Medicine
        const addForm = document.querySelector('form[action*="add_medicine"]');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(addForm, '../Controllers/add_medicine_ajax.php', (data) => {
                    this.showNotification('success', data.message);
                    this.refreshMedicineTable();
                    addForm.reset();
                });
            });
        }

        // Delete Medicine (delegated event)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-medicine-btn')) {
                e.preventDefault();
                const medicineId = e.target.dataset.id;
                if (confirm('Are you sure you want to delete this medicine?')) {
                    this.deleteItem('../Controllers/delete_medicine_ajax.php', { id: medicineId }, () => {
                        this.refreshMedicineTable();
                    });
                }
            }
        });
    }

    setupManufacturerForms() {
        // Add Manufacturer
        const addForm = document.querySelector('form[action*="add_manufacturer"]');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(addForm, '../Controllers/add_manufacturer_ajax.php', (data) => {
                    this.showNotification('success', data.message);
                    this.refreshManufacturerTable();
                    addForm.reset();
                });
            });
        }

        // Delete Manufacturer
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-manufacturer-btn')) {
                e.preventDefault();
                const manufacturerId = e.target.dataset.id;
                if (confirm('Are you sure you want to delete this manufacturer?')) {
                    this.deleteItem('../Controllers/delete_manufacturer_ajax.php', { id: manufacturerId }, () => {
                        this.refreshManufacturerTable();
                    });
                }
            }
        });
    }

    setupFamilyForms() {
        const addForm = document.querySelector('form[action*="add_family_member"]');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(addForm, '../Controllers/add_family_member_ajax.php', (data) => {
                    this.showNotification('success', data.message);
                    this.refreshFamilyList();
                    addForm.reset();
                });
            });
        }

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-family-btn')) {
                e.preventDefault();
                const familyId = e.target.dataset.id;
                if (confirm('Are you sure you want to remove this family member?')) {
                    this.deleteItem('../Controllers/delete_family_member_ajax.php', { id: familyId }, () => {
                        this.refreshFamilyList();
                    });
                }
            }
        });
    }

    setupAppointmentForms() {
        const addForm = document.querySelector('form[action*="add_appointment"]');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(addForm, '../Controllers/add_appointment_ajax.php', (data) => {
                    this.showNotification('success', data.message);
                    if (typeof calendar !== 'undefined') {
                        calendar.refetchEvents();
                    }
                    addForm.reset();
                    this.closeModal();
                });
            });
        }
    }

    setupReportForms() {
        const uploadForm = document.querySelector('form[action*="add_report"]');
        if (uploadForm) {
            uploadForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(uploadForm, '../Controllers/add_report_ajax.php', (data) => {
                    this.showNotification('success', data.message);
                    this.refreshReportsList();
                    uploadForm.reset();
                }, true); // file upload
            });
        }
    }

    setupCounterfeitForms() {
        const reportForm = document.querySelector('form[action*="report_counterfeit"]');
        if (reportForm) {
            reportForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(reportForm, '../Controllers/report_counterfeit_ajax.php', (data) => {
                    this.showNotification('success', data.message);
                    reportForm.reset();
                });
            });
        }
    }

    handleFormSubmit(form, url, successCallback, isFileUpload = false) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        const originalBtnText = submitBtn.value || submitBtn.innerHTML;

        // Disable button and show loading
        submitBtn.disabled = true;
        if (submitBtn.tagName === 'BUTTON') {
            submitBtn.innerHTML = '<span class="spinner"></span> Processing...';
        } else {
            submitBtn.value = 'Processing...';
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successCallback(data);
            } else {
                this.showNotification('error', data.message);
                if (data.data && data.data.errors) {
                    this.displayValidationErrors(form, data.data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            if (submitBtn.tagName === 'BUTTON') {
                submitBtn.innerHTML = originalBtnText;
            } else {
                submitBtn.value = originalBtnText;
            }
        });
    }

    deleteItem(url, data, successCallback) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('success', data.message);
                successCallback();
            } else {
                this.showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('error', 'An error occurred. Please try again.');
        });
    }

    showNotification(type, message) {
        const alert = document.createElement('div');
        alert.className = `ajax-notification ajax-notification-${type}`;
        
        const colors = {
            success: { bg: '#d4edda', color: '#155724', border: '#c3e6cb', icon: '✓' },
            error: { bg: '#f8d7da', color: '#721c24', border: '#f5c6cb', icon: '✗' },
            info: { bg: '#d1ecf1', color: '#0c5460', border: '#bee5eb', icon: 'ℹ' },
            warning: { bg: '#fff3cd', color: '#856404', border: '#ffeeba', icon: '⚠' }
        };
        
        const style = colors[type] || colors.info;
        
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 20px;
            background: ${style.bg};
            color: ${style.color};
            border: 1px solid ${style.border};
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
            max-width: 500px;
        `;
        
        alert.innerHTML = `<strong>${style.icon} ${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}`;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }, type === 'success' ? 3000 : 5000);
    }

    displayValidationErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.error-message').forEach(err => err.remove());
        
        // Display new errors
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.cssText = 'color: #721c24; font-size: 12px; margin-top: 5px;';
                errorDiv.textContent = errors[fieldName];
                field.parentElement.appendChild(errorDiv);
                field.style.borderColor = '#f5c6cb';
            }
        });
    }

    displayVerificationResults(data) {
        const modal = this.createModal('Verification Results', this.formatVerificationResults(data));
        document.body.appendChild(modal);
    }

    formatVerificationResults(data) {
        let html = `
            <div style="max-height: 70vh; overflow-y: auto;">
                <h3 style="color: ${data.verification_result === 'Genuine' ? 'green' : 'red'};">
                    ${data.verification_result}
                </h3>
                <p><strong>Confidence:</strong> ${data.confidence_score}%</p>
        `;
        
        if (data.ai_analysis) {
            const ai = JSON.parse(data.ai_analysis);
            html += `
                <div style="background: #f8f9fa; padding: 15px; margin-top: 15px; border-radius: 5px;">
                    <h4>🤖 AI Analysis</h4>
                    ${this.formatAIAnalysis(ai)}
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }

    formatAIAnalysis(ai) {
        let html = '';
        
        if (ai.analysis_results) {
            if (ai.analysis_results.image_ai) {
                html += `<p><strong>Medicine Detected:</strong> ${ai.analysis_results.image_ai.medicine_name || 'Not detected'}</p>`;
                html += `<p><strong>Manufacturer:</strong> ${ai.analysis_results.image_ai.manufacturer || 'Not detected'}</p>`;
            }
            
            if (ai.analysis_results.counterfeit_ai) {
                const risk = ai.analysis_results.counterfeit_ai;
                html += `
                    <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 5px;">
                        <p><strong>Risk Level:</strong> <span style="color: ${risk.risk_level === 'HIGH' ? 'red' : risk.risk_level === 'MEDIUM' ? 'orange' : 'green'};">${risk.risk_level}</span></p>
                        <p><strong>Risk Score:</strong> ${risk.risk_score}/100</p>
                        <p><strong>Recommendations:</strong></p>
                        <ul>
                            ${risk.recommendations.map(r => `<li>${r}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
        }
        
        return html;
    }

    createModal(title, content) {
        const modal = document.createElement('div');
        modal.className = 'ajax-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        `;
        
        modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">${title}</h2>
                <button onclick="this.closest('.ajax-modal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            ${content}
            <div style="margin-top: 20px; text-align: right;">
                <button onclick="this.closest('.ajax-modal').remove()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        return modal;
    }

    closeModal() {
        const modal = document.querySelector('.ajax-modal');
        if (modal) modal.remove();
    }

    refreshMedicineTable() {
        location.reload(); // Temporary - will implement dynamic table update
    }

    refreshManufacturerTable() {
        location.reload();
    }

    refreshFamilyList() {
        location.reload();
    }

    refreshReportsList() {
        location.reload();
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.6s linear infinite;
        margin-right: 5px;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AjaxHandler();
});
