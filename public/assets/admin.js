// Admin View JavaScript
class AdminQueueManager {
    constructor() {
        this.isQueueActive = true;
        this.totalInQueue = 12;
        this.estimatedWaitTime = 25;
        this.currentlyServing = "A005";
        this.queue = this.generateMockQueue();

        this.initializeElements();
        this.bindEvents();
        this.updateDisplay();
        this.renderAppointments();
    }

    initializeElements() {
        this.elements = {
            adminCurrentlyServing: document.getElementById('adminCurrentlyServing'),
            adminTotalInQueue: document.getElementById('adminTotalInQueue'),
            adminEstimatedWait: document.getElementById('adminEstimatedWait'),
            toggleQueueBtn: document.getElementById('toggleQueueBtn'),
            callNextBtn: document.getElementById('callNextBtn'),
            resetQueueBtn: document.getElementById('resetQueueBtn'),
            statusCurrentlyServing: document.getElementById('statusCurrentlyServing'),
            statusQueueState: document.getElementById('statusQueueState'),
            appointmentsGrid: document.getElementById('appointmentsGrid')
        };
    }

    bindEvents() {
        this.elements.toggleQueueBtn.addEventListener('click', () => {
            this.toggleQueue();
        });

        this.elements.callNextBtn.addEventListener('click', () => {
            this.callNext();
        });

        this.elements.resetQueueBtn.addEventListener('click', () => {
            this.resetQueue();
        });
    }

    generateMockQueue() {
        const names = ['John Smith', 'Sarah Johnson', 'Mike Davis', 'Emily Brown', 'David Wilson', 'Lisa Anderson', 'Chris Taylor', 'Amanda Martinez'];
        const emails = ['john@email.com', 'sarah@email.com', 'mike@email.com', 'emily@email.com', 'david@email.com', 'lisa@email.com', 'chris@email.com', 'amanda@email.com'];
        const phones = ['(555) 123-4567', '(555) 234-5678', '(555) 345-6789', '(555) 456-7890', '(555) 567-8901', '(555) 678-9012', '(555) 789-0123', '(555) 890-1234'];
        const dates = ['Nov 5', 'Nov 5', 'Nov 6', 'Nov 6', 'Nov 7', 'Nov 7', 'Nov 8', 'Nov 8'];
        const times = ['9:00 AM', '10:30 AM', '11:00 AM', '1:00 PM', '2:30 PM', '3:00 PM', '4:30 PM', '5:00 PM'];

        return Array.from({ length: Math.min(8, this.totalInQueue) }, (_, i) => ({
            id: i + 1,
            ticketNumber: `A${String(parseInt(this.currentlyServing.slice(1)) + i + 1).padStart(3, '0')}`,
            name: names[i],
            email: emails[i],
            phone: phones[i],
            appointmentDate: dates[i],
            timeSlot: times[i],
            status: i === 0 ? 'active' : (i < 3 ? 'upcoming' : 'upcoming')
        }));
    }

    updateDisplay() {
        this.elements.adminCurrentlyServing.textContent = this.currentlyServing;
        this.elements.adminTotalInQueue.textContent = this.totalInQueue;
        this.elements.adminEstimatedWait.textContent = `${this.estimatedWaitTime}m`;
        this.elements.statusCurrentlyServing.textContent = this.currentlyServing;

        // Update toggle button
        if (this.isQueueActive) {
            this.elements.toggleQueueBtn.className = 'admin-btn toggle-btn active';
            this.elements.toggleQueueBtn.innerHTML = `
                <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="6" y="4" width="4" height="16"/>
                    <rect x="14" y="4" width="4" height="16"/>
                </svg>
                Pause Queue`;
            this.elements.statusQueueState.textContent = 'Active';
            this.elements.statusQueueState.className = 'status-value active';
        } else {
            this.elements.toggleQueueBtn.className = 'admin-btn toggle-btn inactive';
            this.elements.toggleQueueBtn.innerHTML = `
                <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <polygon points="5,3 19,12 5,21 12,12"/>
                </svg>
                Start Queue`;
            this.elements.statusQueueState.textContent = 'Paused';
            this.elements.statusQueueState.className = 'status-value paused';
        }

        // Update call next button
        this.elements.callNextBtn.disabled = !this.isQueueActive || this.totalInQueue === 0;
    }

    renderAppointments() {
        this.elements.appointmentsGrid.innerHTML = '';

        this.queue.forEach((customer, index) => {
            const card = document.createElement('div');
            card.className = 'appointment-card';

            const statusBadge = this.getStatusBadge(customer.status);

            card.innerHTML = `
                <div class="appointment-header">
                    <div class="ticket-badge">${customer.ticketNumber}</div>
                    <div class="status-badges">
                        ${statusBadge}
                        <span class="position-text">Pos: ${index + 1}</span>
                    </div>
                </div>
                
                <div class="appointment-details">
                    <div class="detail-row">
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="detail-text">${customer.name}</span>
                    </div>
                    
                    <div class="detail-row">
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span class="detail-text">${customer.phone}</span>
                    </div>
                    
                    <div class="detail-row">
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <span class="detail-text">${customer.email}</span>
                    </div>
                    
                    <div class="detail-row">
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        <span class="detail-text">${customer.appointmentDate} at ${customer.timeSlot}</span>
                    </div>
                </div>
            `;

            this.elements.appointmentsGrid.appendChild(card);
        });
    }

    getStatusBadge(status) {
        switch (status) {
            case 'upcoming':
                return '<span class="status-badge upcoming">Upcoming</span>';
            case 'active':
                return '<span class="status-badge active">Active</span>';
            case 'completed':
                return '<span class="status-badge completed">Completed</span>';
            case 'missed':
                return '<span class="status-badge missed">Missed</span>';
            default:
                return '<span class="status-badge upcoming">Upcoming</span>';
        }
    }

    toggleQueue() {
        this.isQueueActive = !this.isQueueActive;
        this.updateDisplay();
    }

    callNext() {
        if (!this.isQueueActive || this.totalInQueue === 0) return;

        // Remove first customer from queue
        this.queue.shift();
        this.totalInQueue--;
        this.estimatedWaitTime = Math.max(5, this.estimatedWaitTime - 2);

        // Generate next ticket number
        const currentNum = parseInt(this.currentlyServing.slice(1));
        this.currentlyServing = `A${String(currentNum + 1).padStart(3, '0')}`;

        // Update first customer status to active
        if (this.queue.length > 0) {
            this.queue[0].status = 'active';
        }

        this.updateDisplay();
        this.renderAppointments();
    }

    resetQueue() {
        if (confirm('Are you sure you want to reset the queue? This will remove all appointments.')) {
            this.totalInQueue = 0;
            this.estimatedWaitTime = 0;
            this.currentlyServing = "A001";
            this.queue = [];

            this.updateDisplay();
            this.renderAppointments();
        }
    }

    // Simulate real-time updates
    startSimulation() {
        setInterval(() => {
            // Randomly add new customers
            if (Math.random() > 0.85 && this.queue.length < 12) {
                this.addNewCustomer();
            }
        }, 3000);
    }

    addNewCustomer() {
        const names = ['Alex Johnson', 'Maria Garcia', 'Robert Lee', 'Jennifer Clark', 'Michael Brown'];
        const name = names[Math.floor(Math.random() * names.length)];

        const newCustomer = {
            id: Date.now(),
            ticketNumber: `A${String(parseInt(this.currentlyServing.slice(1)) + this.queue.length + 1).padStart(3, '0')}`,
            name: name,
            email: `${name.toLowerCase().replace(' ', '.')}@email.com`,
            phone: `(555) ${Math.floor(Math.random() * 900) + 100}-${Math.floor(Math.random() * 9000) + 1000}`,
            appointmentDate: 'Nov 5',
            timeSlot: `${Math.floor(Math.random() * 8) + 9}:00 AM`,
            status: 'upcoming'
        };

        this.queue.push(newCustomer);
        this.totalInQueue++;
        this.estimatedWaitTime += 2;

        this.updateDisplay();
        this.renderAppointments();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const adminManager = new AdminQueueManager();
    adminManager.startSimulation();
});