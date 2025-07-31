// Customer View JavaScript
class CustomerQueueManager {
    constructor() {
        this.isQueueActive = true;
        this.totalInQueue = 12;
        this.estimatedWaitTime = 25;
        this.currentlyServing = "A005";

        this.initializeElements();
        this.bindEvents();
        this.updateDisplay();
    }

    initializeElements() {
        this.elements = {
            currentlyServing: document.getElementById('currentlyServing'),
            totalInQueue: document.getElementById('totalInQueue'),
            estimatedWait: document.getElementById('estimatedWait'),
            appointmentsBooked: document.getElementById('appointmentsBooked'),
            estimatedWaitTime: document.getElementById('estimatedWaitTime'),
            joinQueueBtn: document.getElementById('joinQueueBtn'),
            queueStatus: document.getElementById('queueStatus')
        };
    }

    bindEvents() {
        this.elements.joinQueueBtn.addEventListener('click', () => {
            this.showInfoForm();
        });

        // Form event listeners
        document.getElementById('cancelFormBtn').addEventListener('click', () => {
            this.hideInfoForm();
        });

        document.getElementById('appointmentForm').addEventListener('submit', (e) => {
          //  this.handleFormSubmit(e);
        });
    }

    updateDisplay() {
        this.elements.currentlyServing.textContent = this.currentlyServing;
        this.elements.totalInQueue.textContent = this.totalInQueue;
        this.elements.estimatedWait.textContent = `${this.estimatedWaitTime}m`;
        this.elements.appointmentsBooked.textContent = this.totalInQueue;
        this.elements.estimatedWaitTime.textContent = `${this.estimatedWaitTime}m`;

        // Update button state
        this.elements.joinQueueBtn.disabled = !this.isQueueActive;
        this.elements.joinQueueBtn.innerHTML = this.isQueueActive
            ? `<svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
               </svg>
               Book Your Appointment`
            : 'Booking is Paused';

        // Update status message
        this.elements.queueStatus.textContent = this.isQueueActive
            ? 'Appointment booking is active'
            : 'Appointment booking is currently paused. Please wait for it to resume.';
    }

    showInfoForm() {
        if (!this.isQueueActive) return;

        document.getElementById('joinQueueCard').style.display = 'none';
        document.getElementById('customerInfoForm').style.display = 'block';
    }

    hideInfoForm() {
        document.getElementById('joinQueueCard').style.display = 'block';
        document.getElementById('customerInfoForm').style.display = 'none';
    }

    handleFormSubmit(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const customerInfo = {
            name: formData.get('fullName'),
            phone: formData.get('phoneNumber'),
            email: formData.get('emailAddress'),
            electionDate: formData.get('electionDate'),
            timeSlot: formData.get('timeSlot')
        };

        // Validate form
        if (!customerInfo.name || !customerInfo.phone || !customerInfo.email ||
            !customerInfo.electionDate || !customerInfo.timeSlot) {
            alert('Please fill in all required fields.');
            return;
        }

        // Simulate joining queue
        this.totalInQueue++;
        this.estimatedWaitTime += 2;

        // Show confirmation with customer details
        const confirmationMessage = `
Appointment successfully booked!

Name: ${customerInfo.name}
Phone: ${customerInfo.phone}
Email: ${customerInfo.email}
Date: ${new Date(customerInfo.electionDate).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })}
Time: ${customerInfo.timeSlot}

Your estimated wait time is ${this.estimatedWaitTime} minutes.
        `;

        alert(confirmationMessage);

        // Reset form and hide it
        e.target.reset();
        this.hideInfoForm();
        this.updateDisplay();
    }

    // Simulate real-time updates
    startSimulation() {
        setInterval(() => {
            // Randomly update queue
            if (Math.random() > 0.7) {
                this.callNext();
            }

            if (Math.random() > 0.8) {
                this.addCustomer();
            }
        }, 5000);
    }

    callNext() {
        if (this.totalInQueue > 0) {
            this.totalInQueue--;
            this.estimatedWaitTime = Math.max(5, this.estimatedWaitTime - 2);

            // Generate next ticket number
            const currentNum = parseInt(this.currentlyServing.slice(1));
            this.currentlyServing = `A${String(currentNum + 1).padStart(3, '0')}`;

            this.updateDisplay();
        }
    }

    addCustomer() {
        this.totalInQueue++;
        this.estimatedWaitTime += 2;
        this.updateDisplay();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const queueManager = new CustomerQueueManager();
    queueManager.startSimulation();
});
