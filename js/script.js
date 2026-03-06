// ==============================================
// INITIALISATION
// ==============================================
lucide.createIcons();
let TemplateEditor = localStorage.getItem('chosenTemplate') || 'modern';
let photoData = null;

// ==============================================
// GESTION DE LA PHOTO
// ==============================================
$('#in-photo').on('change', function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(ev){
            photoData = ev.target.result;
            $('#preview-photo').html(`<img src="${photoData}" class="w-full h-full object-cover rounded-full">`);
        };
        reader.readAsDataURL(file);
    } else {
        photoData = null;
        $('#preview-photo').html(`<span class="text-slate-400">Photo</span>`);
    }
});

// ==============================================
// VALIDATION NUMÉRO SÉNÉGAL
// ==============================================
function isValidSenegalPhone(phone){
    return /^(?:\+221\s?|0)(7[0678]|76|77|78|70|75|79)\s?\d{2}\s?\d{2}\s?\d{2}$/.test(phone);
}

$('#in-phone').on('blur', function(){
    const phone = $(this).val();
    if(!isValidSenegalPhone(phone)){
        alert("Numéro invalide ! Format Sénégal attendu: +221 XX XXX XX XX ou 0X XXX XX XX");
        $(this).addClass('invalid');
    } else {
        $(this).removeClass('invalid');
    }
});

// ==============================================
// PRÉVISUALISATION DYNAMIQUE
// ==============================================
function updatePreviewEditor(){
    $('#preview-name').text($('#in-name').val() || 'Nom Complet');
    $('#preview-title').text($('#in-title').val() || 'Poste Visé');
    $('#preview-bio').text($('#in-bio').val() || 'À propos de vous...');
    $('#preview-email').text($('#in-email').val() || 'exemple@mail.com');
    $('#preview-phone').text($('#in-phone').val() || '+221 77 123 45 67');
    $('#preview-location').text($('#in-location').val() || 'Ville, Pays');
    $('#preview-linkedin').text($('#in-linkedin').val() || 'linkedin.com/in/exemple');

    const headerColor = $('#color-header').val() || '#4f46e5';
    const titleColor = $('#color-title').val() || '#4f46e5';
    const textColor = $('#color-text').val() || '#000000';

    $('#cv-preview-container').css('--color-header', headerColor)
                              .css('--color-title', titleColor)
                              .css('--color-text', textColor);

    $('#cv-preview-container h2, #cv-preview-container h3').css('color', titleColor);
    $('#cv-preview-container p, #cv-preview-container li, #cv-preview-container span').css('color', textColor);

    if(TemplateEditor==='creative') $('#cv-header').css('background-color', headerColor);
    else if(TemplateEditor==='modern') $('#cv-left').css('background-color', headerColor);

    // Expériences
    let expHTML = '';
    $('#experience-list > div').each(function(){
        const company = $(this).find('.in-exp-company').val();
        const date = $(this).find('.in-exp-date').val();
        const desc = $(this).find('.in-exp-desc').val();
        if(company || date || desc) expHTML += `<p><b>${company}</b> (${date}): ${desc}</p>`;
    });
    $('#preview-experience').html(expHTML || '<p>Aucune expérience ajoutée</p>');

    // Compétences
    const skills = $('#skill-list input').map(function(){ return $(this).val(); }).get().filter(Boolean);
    $('#preview-skills').html(skills.map(s => `<li class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded">${s}</li>`).join(''));

    // Langues
    const langs = $('#lang-list input').map(function(){ return $(this).val(); }).get().filter(Boolean);
    $('#preview-langs').html(langs.map(l => `<li class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded">${l}</li>`).join(''));

    // Centres d'intérêt
    const interests = $('#interest-list input').map(function(){ return $(this).val(); }).get().filter(Boolean);
    $('#preview-interests').html(interests.map(i => `<li class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded">${i}</li>`).join(''));

    lucide.createIcons();
}

// ==============================================
// APPLICATION DU TEMPLATE
// ==============================================
function applyTemplateStyleEditor(template){
    TemplateEditor = template;
    const container = $('#cv-preview-container');
    container.removeClass('template-modern template-minimal template-creative');

    if(template==='modern') container.addClass('template-modern');
    else if(template==='minimal') container.addClass('template-minimal');
    else if(template==='creative') container.addClass('template-creative');

    updatePreviewEditor();
}

// ==============================================
// EXPORT PDF
// ==============================================
function exportPDF(){
    html2pdf(document.getElementById('cv-preview-container'), {
        margin:0.5,
        filename:'Mon_CV.pdf',
        image:{type:'jpeg', quality:0.98},
        html2canvas:{scale:2, scrollY:0},
        jsPDF:{unit:'in', format:'a4', orientation:'portrait'}
    });
}

// ==============================================
// SAUVEGARDE DU CV
// ==============================================
$('#save-cv-btn').off('click').on('click', async function(){
    const user = JSON.parse(localStorage.getItem('user'));
    if(!user){
        alert('⚠️ Connectez-vous pour enregistrer le CV.');
        return;
    }

    const phone = $('#in-phone').val();
    if(!isValidSenegalPhone(phone)){
        alert('⚠️ Numéro de téléphone invalide. Format Sénégal attendu.');
        $('#in-phone').focus();
        return;
    }

    const cvData = {
        user_id: user.id,
        template: TemplateEditor,
        content: $('#cv-preview-container').html(),
        photo: photoData
    };

    try{
        const res = await fetch('api/save_cv.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify(cvData)
        });
        const result = await res.json();
        if(result.success){
            Swal.fire({
                icon: 'success',
                title: 'CV enregistré !',
                text: 'Votre CV a été sauvegardé avec succès.',
                confirmButtonColor: '#4f46e5'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: result.message,
                confirmButtonColor: '#4f46e5'
            });
        }
    }catch(err){
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de l’enregistrement du CV.',
            confirmButtonColor: '#4f46e5'
        });
    }
});

// ==============================================
// INPUT DYNAMIQUE
// ==============================================
$(document).on('input', '.cv-input, .cv-textarea, #color-header, #color-title, #color-text', updatePreviewEditor);

// ==============================================
// INITIALISATION AU CHARGEMENT
// ==============================================
$(document).ready(function(){
    if($('#cv-preview-container').length){
        applyTemplateStyleEditor(TemplateEditor);

        // Déplacer le bouton sauvegarder sous le formulaire pour plus de visibilité
        const saveBtn = $('#save-cv-btn').detach();
        $('.col-span-1').append(saveBtn.addClass('mt-4 w-full py-3 font-bold text-white rounded-xl shadow-lg gradient-bg hover:opacity-90'));
    }
});