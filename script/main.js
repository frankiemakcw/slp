const { jsPDF } = window.jspdf;

// Common function to generate PDF header and student info
function generatePDFHeader(doc, data, pageY = 20) {
    const { stuName, stuClass, stuClassNum, stuID, start_year, end_year, issue_date } = data;

    // Header
    doc.text('YING WA GIRLS\' SCHOOL', 105, pageY, { align: 'center' });
    doc.text(`OTHER LEARNING EXPERIENCES (20${start_year}-20${end_year})`, 105, pageY + 4, { align: 'center' });
    doc.text('STUDENT-REPORTED DATA', 105, pageY + 8, { align: 'center' });

    // underline for header
    doc.line(84.5, pageY + 0.5, 125.5, pageY + 0.5);
    doc.line(68.5, pageY + 4.5, 141.7, pageY + 4.5);
    doc.line(82.4, pageY + 8.5, 127.8, pageY + 8.5);
    
    // Student Info
    doc.text('NAME:', 15, pageY + 18);
    doc.text('CLASS:', 15, pageY + 25);
    doc.text('STUDENT ID:', 120, pageY + 18);
    doc.text('DATE OF ISSUE:', 120, pageY + 25);
    
    doc.text(stuName, 30, pageY + 18);
    doc.text(`${stuClass} (${stuClassNum})`, 30, pageY + 25);
    doc.text(stuID, 150, pageY + 18);
    doc.text(issue_date, 150, pageY + 25);
}

// Common function to generate PDF footer
function generatePDFFooter(doc) {
    doc.line(130, 275, 190, 275);
    doc.text('STUDENT\'S SIGNATURE', 160, 280, { align: 'center' });
}

// Common function to setup PDF document
function setupPDFDocument(data) {
    const { stuClass, stuClassNum, start_year, end_year } = data;
    
    const doc = new jsPDF({ lineHeight: 1.2 });
    
    // Set document properties
    const classNumStr = stuClassNum < 10 ? `0${stuClassNum}` : stuClassNum;
    doc.setProperties({
        title: `SLP_${start_year}${end_year}_${stuClass}${classNumStr}.pdf`
    });
    
    doc.setFont('notosanstcedit', 'normal'); 
    doc.setFontSize(10);
    doc.setDrawColor(0);
    doc.setLineWidth(0.2);
    
    return doc;
}

// Common function to generate activities page
function generateActivitiesPage(doc, data) {
    const { activities } = data;
    
    generatePDFHeader(doc, data);
    
    // Section header
    doc.text('PART I: ACTIVITIES / PROGRAMMES / COMPETITIONS', 15, 55);

    // claim
    doc.text('I hereby declare that I have participated in the activities above.', 18, 245);
    doc.line(18, 239.5, 192, 239.5);
    doc.line(18, 239, 192, 239);

    // Draw rectangle
    doc.rect(15, 60, 180, 190, 'S');

    generatePDFFooter(doc);
    
    // Prepare table data
    const tableData = activities
        .sort((a, b) => new Date(a.start_date) - new Date(b.start_date))
        .map((activity, index) => [
            `${index + 1}.`,
            formatActivityDate(activity.start_date, activity.end_date),
            activity.activity_name,
            activity.organizer,
            activity.role
        ]);

    // Add table with hooks to check position
    doc.autoTable({
        head: [['', 'DATE', 'ACTIVITY / PROGRAMME / COMPETITION', 'ORGANIZED BY', 'ROLE']],
        body: tableData,
        startY: 65,
        columnStyles: {
            0: { cellWidth: 8 },  
            1: { cellWidth: 26 },
            2: { cellWidth: 70 },
            3: { cellWidth: 46 },
            4: { cellWidth: 24 }
        },
        styles: {
            font: 'notosanstcedit', 
            fontStyle: 'normal',
            fontSize: 10,
            cellPadding: { top: 1, right: 1.5, bottom: 1, left: 1.5 },
            textColor: 0,
            halign: 'left' 
        },
        headStyles: {
            fillColor: false,
            cellPadding: { top: 1, right: 1.5, bottom: 1, left: 1.5 },
            fontStyle: 'normal',
            halign: 'left' 
        },
        alternateRowStyles: {
            cellPadding: { top: 1, right: 1.5, bottom: 1, left: 1.5 },
            fillColor: false
        },
        margin: { left: 18, right: 18 },
        didDrawPage: (data) => {
            // Check if the table has reached or exceeded our threshold
            if (data.cursor.y >= 239) {
                throw new Error('Warning: Too many activities. Please preview the PDF and reduce the number of activities.');
            }
        }
    });
    
}

// Common function to generate reflections page
function generateReflectionsPage(doc, data) {
    const { reflection } = data;
    
    doc.addPage();
    generatePDFHeader(doc, data);
    
    // Section header
    doc.text('PART II:  PERSONAL REFLECTIONS', 15, 55);

    // Draw rectangle
    doc.rect(15, 60, 180, 190, 'S');

    generatePDFFooter(doc);

    // Reflection
    doc.text(reflection, 20, 68, {
        maxWidth: 170,
        align: 'left'
    });

    // Calculate approximate number of lines the reflection will take
    const lines = doc.splitTextToSize(reflection, 170);
    
    // Check if text will exceed available space
    if (lines.length > 43) {
        throw new Error('Warning: Your reflection is too long. Please preview the PDF and shorten the reflection.');
    }

    
}

async function submitPDF() {
    const submitBtn = document.querySelector('#submitpdf');
    try {
        const response_info = await fetch('getdata_json.php');
        if (!response_info.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response_info.json();
        
        const doc = setupPDFDocument(data);
        generateActivitiesPage(doc, data);
        if (data.stuClass.startsWith('3') || data.stuClass.startsWith('6')) {
            generateReflectionsPage(doc, data);
        }
        
        // Generate PDF blob
        const pdfBlob = doc.output('blob');
        
        // Create FormData to send
        const formData = new FormData();
        formData.append('pdf', pdfBlob, `slp.pdf`);
        formData.append('student_id', data.stuID);
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        // Send to server
        const response = await fetch('submit_slp.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('SLP submitted successfully!');
            window.location.reload();
        } else {
            throw new Error('Submission failed: ' + result.message);
        }
    } catch (error) {
        console.error('Submission error:', error);
        alert(error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';
    }
}

async function savepreview() {

    const response_info = await fetch('getdata_json.php');
    if (!response_info.ok) {
        throw new Error('Network response was not ok');
    }
    const data = await response_info.json();
    
    const doc = setupPDFDocument(data);
    try {
        generateActivitiesPage(doc, data);
    } catch (activitiesError) {

    }

    if (data.stuClass.startsWith('3') || data.stuClass.startsWith('6')) {
        try {
            generateReflectionsPage(doc, data);
        } catch (reflectionsError) {

        }
    }
    
    // Generate PDF blob
    const pdfBlob = doc.output('blob');
    
    // Create FormData to send
    const formData = new FormData();
    formData.append('pdf', pdfBlob, `slp.pdf`);
    formData.append('student_id', data.stuID);
    
    // Send to server
    const response = await fetch('save_preview.php', {
        method: 'POST',
        body: formData
    });      
        
}

document.addEventListener('DOMContentLoaded', savepreview);

function deleteActivity(id) {
    if (confirm('Are you sure you want to delete this activity?')) {
        fetch('delete_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Refresh the page after deletion
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the activity');
        });
    }
}

// Helper function to format dates
function formatActivityDate(startDate, endDate) {
    const format = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', {
            year: 'numeric', month: '2-digit', day: '2-digit'
        }).replace(/\//g, '/');
    };
    
    return startDate === endDate 
        ? format(startDate) 
        : `${format(startDate)} - ${format(endDate)}`;
}
