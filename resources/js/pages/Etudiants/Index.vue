<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch, computed, nextTick } from 'vue'




const props = defineProps({
    etudiants: {
        type: Array,
        type: String,
        default: () => [],
    },

    classes: {
        type: Array,
        default: () => [],
    },

    series: {
        type: Array,
        default: () => [],
    },

    anneeScolaires: {
        type: Array,
        default: () => [],
    },

    anneeActive: {
        type: String,
        default: '',
    },

    filters: {
        type: Object,
        default: () => ({}),
    },
})

const localEtudiants = ref([])
const search = ref(props.filters?.search || '')
const showCreate = ref(false)
const showModal = ref(false)
const showSidebar = ref(true)
const selected = ref(null)
const selectedParents = ref(null)
const selectedInfo = ref(null)
const activeActionId = ref(null)
const toast = ref('')
const toastColor = ref('blue')

const selectedClasseFilter = ref('')
const selectedSerieFilter = ref('')
const selectedAnneeFilter = ref(
    props.filters?.annee_scolaire || props.anneeActive || ''
)

function filterByAnnee() {
    router.get('/etudiants', {
        search: search.value,
        annee_scolaire: selectedAnneeFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function todayDate() {
    return new Date().toISOString().slice(0, 10)
}

function getDateInscription(e) {
    return e?.inscription?.date_inscription || e?.date_inscription || todayDate()
}

function getClasse(e) {
    return e?.inscription?.classe || e?.classe?.nom_classe || e?.classe || 'Non défini'
}

function getNiveau(e) {
    return e?.inscription?.niveau || e?.classe?.niveau?.nom_niveau || e?.niveau || 'Non défini'
}

function getSerie(e) {
    return e?.inscription?.section || e?.inscription?.serie || e?.serie?.nom_serie || e?.section || '-'
}

function getPhoto(e) {
    if (!e?.photo) return ''
    if (String(e.photo).startsWith('http')) return e.photo
    if (String(e.photo).startsWith('/storage')) return e.photo
    return `/storage/${e.photo}`
}

function toggleActions(id) {
    activeActionId.value = activeActionId.value === id ? null : id
}

function openInfo(e) {
    selectedInfo.value = e
    activeActionId.value = null
}

function closeInfo() {
    selectedInfo.value = null
}

function printStudentPdf(e) {
    const photo = getPhoto(e)
    const win = window.open('', '_blank')

    win.document.write(`
        <html>
        <head>
            <title>Fiche étudiant</title>
            <style>
                @page { size: A4; margin: 7mm; }
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    color: #0f172a;
                    background: #fff;
                    font-size: 9.5px;
                }
                .sheet {
                    width: 100%;
                    max-width: 760px;
                    margin: auto;
                    border: 1px solid #dbe4ee;
                    padding: 9px;
                    border-radius: 10px;
                    page-break-inside: avoid;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 2px solid #0f766e;
                    padding-bottom: 6px;
                    margin-bottom: 6px;
                }
                .school h1 {
                    margin: 0;
                    color: #0f766e;
                    font-size: 16px;
                }
                .school p {
                    margin: 1px 0;
                    font-size: 9px;
                    color: #64748b;
                }
                .photo, .photo-placeholder {
                    width: 64px;
                    height: 64px;
                    border-radius: 10px;
                    border: 2px solid #e2e8f0;
                    object-fit: cover;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #e2e8f0;
                    font-size: 24px;
                }
                h2 {
                    margin: 5px 0;
                    text-align: center;
                    font-size: 13px;
                    text-transform: uppercase;
                }
                .grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 5px;
                }
                .section {
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    overflow: hidden;
                    margin-bottom: 5px;
                }
                .section-title {
                    background: #0f766e;
                    color: white;
                    padding: 4px 7px;
                    font-weight: bold;
                    font-size: 9.5px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 9.2px;
                }
                td {
                    border-top: 1px solid #e2e8f0;
                    padding: 3px 5px;
                    vertical-align: top;
                }
                td.label {
                    width: 34%;
                    background: #f8fafc;
                    font-weight: bold;
                    color: #334155;
                }
                .footer {
                    margin-top: 6px;
                    display: flex;
                    justify-content: space-between;
                    font-size: 9px;
                    color: #475569;
                }
                @media print {
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .sheet { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="sheet">
                <div class="header">
                    <div class="school">
                        <h1>Strelitzia School</h1>
                        <p>Fiche complète d'information étudiant</p>
                        <p>Année scolaire : ${e.annee_scolaire || '2025-2026'}</p>
                    </div>
                    ${
                        photo
                            ? `<img src="${photo}" class="photo" />`
                            : `<div class="photo-placeholder">👤</div>`
                    }
                </div>

                <h2>Fiche étudiant</h2>

                <div class="grid">
                    <div class="section">
                        <div class="section-title">Informations étudiant</div>
                        <table>
                            <tr><td class="label">Matricule</td><td>${e.numero || '-'}</td></tr>
                            <tr><td class="label">Nom</td><td>${e.nom || '-'}</td></tr>
                            <tr><td class="label">Prénom(s)</td><td>${e.prenoms || '-'}</td></tr>
                            <tr><td class="label">Sexe</td><td>${e.sexe || '-'}</td></tr>
                            <tr><td class="label">Date inscription</td><td>${getDateInscription(e)}</td></tr>
                            <tr><td class="label">Date naissance</td><td>${e.date_naissance || '-'}</td></tr>
                            <tr><td class="label">Lieu naissance</td><td>${e.lieu_naissance || '-'}</td></tr>
                        </table>
                    </div>

                    <div class="section">
                        <div class="section-title">Scolarité & contact</div>
                        <table>
                            <tr><td class="label">Classe</td><td>${getClasse(e)}</td></tr>
                            <tr><td class="label">Niveau</td><td>${getNiveau(e)}</td></tr>
                            <tr><td class="label">Série</td><td>${getSerie(e)}</td></tr>
                            <tr><td class="label">Téléphone</td><td>${e.telephone || e.contact || '-'}</td></tr>
                            <tr><td class="label">Email</td><td>${e.email || '-'}</td></tr>
                            <tr><td class="label">Adresse</td><td>${e.adresse || '-'}</td></tr>
                            <tr><td class="label">Site</td><td>${e.site || 'Strelitzia School'}</td></tr>
                        </table>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Parents / Tuteur</div>
                    <table>
                        <tr>
                            <td class="label">Père</td><td>${e.inscription?.pere_nom || '-'}</td>
                            <td class="label">Tél. père</td><td>${e.inscription?.pere_telephone || '-'}</td>
                        </tr>
                        <tr>
                            <td class="label">Profession père</td><td>${e.inscription?.pere_profession || '-'}</td>
                            <td class="label">Mère</td><td>${e.inscription?.mere_nom || '-'}</td>
                        </tr>
                        <tr>
                            <td class="label">Tél. mère</td><td>${e.inscription?.mere_telephone || '-'}</td>
                            <td class="label">Profession mère</td><td>${e.inscription?.mere_profession || '-'}</td>
                        </tr>
                        <tr>
                            <td class="label">Tuteur</td><td>${e.inscription?.tuteur_nom || '-'}</td>
                            <td class="label">Tél. tuteur</td><td>${e.inscription?.tuteur_telephone || '-'}</td>
                        </tr>
                        <tr>
                            <td class="label">Lien</td><td>${e.inscription?.tuteur_lien || '-'}</td>
                            <td class="label">Adresse tuteur</td><td>${e.inscription?.tuteur_adresse || '-'}</td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <span>Fait le : ${todayDate()}</span>
                    <span>Signature administration : ____________________</span>
                </div>
            </div>

            <script>
                window.onload = function() {
                    setTimeout(function() { window.print(); }, 300);
                }
            <\/script>
        </body>
        </html>
    `)

    win.document.close()
}

function syncEtudiants(list) {
    localEtudiants.value = (list || []).map(e => ({
        ...e,
        show: e.show || false,
    }))
}

watch(() => props.etudiants, value => syncEtudiants(value), {
    immediate: true,
    deep: true,
})

const activeClasses = computed(() => {
    if (!props.anneeActive) {
        return props.classes || []
    }

    return (props.classes || []).filter(classe =>
        (classe.annee_scolaire || '') === props.anneeActive
    )
})

const form = useForm({
    nom: '',
    prenoms: '',
    sexe: '',
    classe_id: '',
    serie_id: '',
    email: '',
    telephone: '',
    adresse: '',
})

const editForm = useForm({
    nom: '',
    prenoms: '',
    sexe: '',
    date_naissance: '',
    lieu_naissance: '',
    classe_id: '',
    serie_id: '',
    email: '',
    telephone: '',
    adresse: '',
    photo: null,
})

const parentForm = useForm({
    pere_nom: '',
    pere_telephone: '',
    pere_profession: '',
    mere_nom: '',
    mere_telephone: '',
    mere_profession: '',
    tuteur_nom: '',
    tuteur_telephone: '',
    tuteur_lien: '',
    tuteur_adresse: '',
})

const formSeries = computed(() => {
    const classe = props.classes.find(c => Number(c.id) === Number(form.classe_id))
    return classe?.series || []
})

const editSeries = computed(() => {
    const classe = props.classes.find(c => Number(c.id) === Number(editForm.classe_id))
    return classe?.series || []
})

const filterSeries = computed(() => {
    const classe = props.classes.find(c => Number(c.id) === Number(selectedClasseFilter.value))
    return classe?.series || []
})

const filteredEtudiants = computed(() => {
    return localEtudiants.value.filter(e => {
        const okClasse = !selectedClasseFilter.value || Number(e.classe_id) === Number(selectedClasseFilter.value)
        const okSerie = !selectedSerieFilter.value || Number(e.serie_id) === Number(selectedSerieFilter.value)
        const okAnnee = !selectedAnneeFilter.value || String(e.annee_scolaire || '') === String(selectedAnneeFilter.value)

        return okClasse && okSerie && okAnnee
    })
})

watch(() => form.classe_id, () => {
    form.serie_id = ''
})

watch(() => editForm.classe_id, () => {
    editForm.serie_id = ''
})

watch(selectedClasseFilter, () => {
    selectedSerieFilter.value = ''
})

function showParents(etudiant) {
    selectedParents.value = etudiant
    activeActionId.value = null

    parentForm.pere_nom = etudiant.inscription?.pere_nom || ''
    parentForm.pere_telephone = etudiant.inscription?.pere_telephone || ''
    parentForm.pere_profession = etudiant.inscription?.pere_profession || ''
    parentForm.mere_nom = etudiant.inscription?.mere_nom || ''
    parentForm.mere_telephone = etudiant.inscription?.mere_telephone || ''
    parentForm.mere_profession = etudiant.inscription?.mere_profession || ''
    parentForm.tuteur_nom = etudiant.inscription?.tuteur_nom || ''
    parentForm.tuteur_telephone = etudiant.inscription?.tuteur_telephone || ''
    parentForm.tuteur_lien = etudiant.inscription?.tuteur_lien || ''
    parentForm.tuteur_adresse = etudiant.inscription?.tuteur_adresse || ''
}

function closeParents() {
    selectedParents.value = null
    parentForm.reset()
}

function updateParents() {
    if (!selectedParents.value?.id) return

    parentForm.post(`/etudiants/${selectedParents.value.id}/parents`, {
        preserveScroll: true,
        onSuccess: () => {
            closeParents()
            showToast('✅ Informations parents modifiées', 'blue')

            router.reload({
                only: ['etudiants', 'classes'],
                preserveScroll: true,
                onSuccess: page => syncEtudiants(page.props.etudiants),
            })
        },
        onError: () => {
            showToast('❌ Modification parents impossible', 'red')
        },
    })
}

function showToast(message, color = 'blue') {
    toast.value = message
    toastColor.value = color
    setTimeout(() => toast.value = '', 3000)
}

function validateForm(targetForm) {
    targetForm.clearErrors()

    if (
        !targetForm.nom ||
        !targetForm.prenoms ||
        !targetForm.sexe ||
        !targetForm.classe_id ||
        !targetForm.telephone ||
        !targetForm.adresse
    ) {
        showToast('⚠️ Information incomplète', 'red')

        if (!targetForm.nom) targetForm.setError('nom', 'Le nom est obligatoire')
        if (!targetForm.prenoms) targetForm.setError('prenoms', 'Le prénom est obligatoire')
        if (!targetForm.sexe) targetForm.setError('sexe', 'Le sexe est obligatoire')
        if (!targetForm.classe_id) targetForm.setError('classe_id', 'La classe est obligatoire')
        if (!targetForm.telephone) targetForm.setError('telephone', 'Le téléphone est obligatoire')
        if (!targetForm.adresse) targetForm.setError('adresse', 'L’adresse est obligatoire')

        return false
    }

    return true
}

function refreshPage() {
    window.location.reload()
}

function submit() {
    if (!validateForm(form)) return

    form.post('/etudiants', {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            form.reset()
            showCreate.value = false
            showToast('✅ Nouvel étudiant ajouté avec succès', 'blue')

            router.reload({
                only: ['etudiants', 'classes'],
                preserveScroll: true,
                preserveState: false,
                onSuccess: page => syncEtudiants(page.props.etudiants),
            })
        },

        onError: () => {
            showToast('❌ Information incomplète ou erreur controller', 'red')
        },
    })
}

async function openEdit(e) {
    if (!e?.id) return

    activeActionId.value = null
    selected.value = e
    editForm.clearErrors()

    editForm.nom = e.nom || ''
    editForm.prenoms = e.prenoms || ''
    editForm.sexe = e.sexe || ''
    editForm.date_naissance = e.date_naissance || ''
    editForm.lieu_naissance = e.lieu_naissance || ''
    editForm.classe_id = e.classe_id ? String(e.classe_id) : ''
    editForm.email = e.email || ''
    editForm.telephone = e.telephone || e.contact || ''
    editForm.adresse = e.adresse || ''
    editForm.photo = null

    await nextTick()
    editForm.serie_id = e.serie_id ? String(e.serie_id) : ''

    showModal.value = true
}

function closeModal() {
    showModal.value = false
    selected.value = null
    editForm.reset()
    editForm.clearErrors()
}
function handleEditPhoto(e) {
    const file = e.target.files[0]
    editForm.photo = file || null
}

function update() {
    if (!selected.value?.id) return
    if (!validateForm(editForm)) return

    editForm
    .transform(data => ({
        ...data,
        _method: 'PUT',
    }))
    .post(`/etudiants/${selected.value.id}`, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            editForm.transform(data => data)
            closeModal()

            router.reload({
                only: ['etudiants', 'classes'],
                preserveScroll: true,
                preserveState: false,
                onSuccess: page => {
                    syncEtudiants(page.props.etudiants)
                    showToast('✏️ Modification validée', 'blue')
                },
            })
        },

        onError: () => {
            showToast('❌ Modification impossible', 'red')
        },
    })
}

function remove(id) {
    if (!id) return
    activeActionId.value = null

    if (confirm('Voulez-vous vraiment supprimer cet étudiant ?')) {
        router.post(`/etudiants/${id}`, { _method: 'DELETE' }, {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                showToast('🗑️ Étudiant supprimé', 'red')

                router.reload({
                    only: ['etudiants', 'classes'],
                    preserveScroll: true,
                    preserveState: false,
                    onSuccess: page => syncEtudiants(page.props.etudiants),
                })
            },
        })
    }
}

function exportExcel() {
    if (filteredEtudiants.value.length === 0) {
        showToast('Aucune donnée à exporter', 'red')
        return
    }

    const rows = filteredEtudiants.value.map(e => `
        <tr>
            <td>${e.numero || e.id || ''}</td>
            <td>${e.site || ''}</td>
            <td>${e.annee_scolaire || ''}</td>
            <td>${getDateInscription(e)}</td>
            <td>${e.nom || ''}</td>
            <td>${e.prenoms || ''}</td>
            <td>${e.sexe || ''}</td>
            <td>${getClasse(e)}</td>
            <td>${getNiveau(e)}</td>
            <td>${getSerie(e)}</td>
            <td>${e.telephone || e.contact || ''}</td>
            <td>${e.date_naissance || ''}</td>
            <td>${e.lieu_naissance || ''}</td>
            <td>${e.email || ''}</td>
            <td>${e.adresse || ''}</td>
        </tr>
    `).join('')

    const html = `
        <table border="1">
            <thead>
                <tr>
                    <th>M°</th>
                    <th>Site</th>
                    <th>AS</th>
                    <th>Date inscription</th>
                    <th>Nom</th>
                    <th>Prénom(s)</th>
                    <th>Sexe</th>
                    <th>Classe</th>
                    <th>Niveau</th>
                    <th>Série</th>
                    <th>Contact</th>
                    <th>Date Naiss.</th>
                    <th>Lieu Naiss.</th>
                    <th>Email</th>
                    <th>Adresse</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>
    `

    const blob = new Blob([html], { type: 'application/vnd.ms-excel' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'etudiants.xls'
    link.click()
    URL.revokeObjectURL(url)

    showToast('✅ Export Excel généré', 'blue')
}

function printPdf() {
    const rows = filteredEtudiants.value.map(e => `
        <tr>
            <td>${e.numero || e.id || ''}</td>
            <td>${e.site || ''}</td>
            <td>${e.annee_scolaire || ''}</td>
            <td>${getDateInscription(e)}</td>
            <td>${e.nom || ''}</td>
            <td>${e.prenoms || ''}</td>
            <td>${e.sexe || ''}</td>
            <td>${getClasse(e)}</td>
            <td>${getNiveau(e)}</td>
            <td>${getSerie(e)}</td>
            <td>${e.telephone || e.contact || ''}</td>
            <td>${e.date_naissance || ''}</td>
            <td>${e.lieu_naissance || ''}</td>
        </tr>
    `).join('')

    const printWindow = window.open('', '_blank')

    printWindow.document.write(`
        <html>
            <head>
                <title>Liste des étudiants</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { font-size: 22px; margin-bottom: 15px; }
                    table { width: 100%; border-collapse: collapse; font-size: 10px; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                    th { background: #f1f5f9; }
                </style>
            </head>
            <body>
                <h1>Liste des étudiants</h1>
                <table>
                    <thead>
                        <tr>
                            <th>M°</th>
                            <th>Site</th>
                            <th>AS</th>
                            <th>Date inscription</th>
                            <th>Nom</th>
                            <th>Prénom(s)</th>
                            <th>Sexe</th>
                            <th>Classe</th>
                            <th>Niveau</th>
                            <th>Série</th>
                            <th>Contact</th>
                            <th>Date Naiss.</th>
                            <th>Lieu Naiss.</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </body>
        </html>
    `)

    printWindow.document.close()
    printWindow.focus()
    printWindow.print()
}

watch(search, value => {
    router.get('/etudiants', { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: page => syncEtudiants(page.props.etudiants),
    })
})
</script>

<template>
    <Head title="Liste des étudiants" />

    <div class="min-h-screen bg-slate-100 text-slate-900">
        <div
            v-if="toast"
            :class="[
                'fixed top-4 right-4 z-[999] rounded-2xl px-5 py-3 text-sm font-bold text-white shadow-2xl',
                toastColor === 'blue' ? 'bg-blue-600' : 'bg-red-600'
            ]"
        >
            {{ toast }}
        </div>

        <div class="flex min-h-screen">
 <aside
    :class="[
        'fixed top-0 left-0 z-50 w-64 h-screen overflow-y-auto bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white shadow-2xl transition-transform duration-300',
        showSidebar ? 'translate-x-0' : '-translate-x-full'
    ]"
>

    <div class="sticky top-0 z-10 bg-slate-950">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
            <div class="flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-xl shadow-lg">
                    🎓
                </div>

                <div>
                    <h1 class="text-sm font-black leading-tight">
                        Misaela Musique
                    </h1>

                    <p class="text-[10px] text-slate-400">
                        Gestion scolaire
                    </p>
                </div>
            </div>

            <button
                type="button"
                @click="showSidebar = false"
                class="lg:hidden rounded-xl bg-white/10 px-3 py-2"
            >
                ✕
            </button>
        </div>
    </div>

    <div class="p-5">
        <div class="flex items-center gap-3 rounded-3xl bg-white/10 p-3">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-100 text-2xl">
                👤
            </div>

            <div>
                <p class="text-sm font-bold">
                    Administrateur
                </p>

                <p class="text-xs text-slate-300">
                    ● En ligne
                </p>
            </div>
        </div>
    </div>

    <nav class="space-y-1 px-3 pb-10 text-sm">
        <a href="#" class="menu-link">
            🏠 Tableau de bord
        </a>

        <a href="#" class="menu-link">
            💰 Gestion financière
        </a>

        <div class="rounded-2xl bg-teal-500 px-4 py-3 font-bold text-white shadow-lg">
            👥 Étudiants
        </div>

        <a href="/etudiants" class="submenu active">
            -Liste des inscrits
        </a>

        <a href="/inscriptions" class="submenu active">
            -Inscrire un étudiant
        </a>
        <a href="/reinscriptions" class="submenu active">
            🔁 Réinscription
        </a>

        <a href="/niveaux" class="submenu active">
            Liste des niveaux
        </a>

        <a href="/classes" class="submenu active">
            Liste des classes
        </a>

        <a href="/series" class="submenu active">
            Liste des séries
        </a>

        <div class="mt-4 rounded-2xl bg-slate-800 px-4 py-3 font-bold text-white shadow-lg">
            ⚙️ Paramètres
        </div>

        <a href="/matricule" class="submenu active">
            🔢 Matricule
        </a>

        <a href="/annee-scolaire" class="submenu active">
            📅 Année Scolaire
        </a>
    </nav>

</aside>



                                            <div
                                                v-if="showSidebar"
                                                @click="showSidebar = false"
                                                class="fixed inset-0 z-40 bg-black/50 lg:hidden"
                                            ></div>

                                            <main
                                                :class="[
                                                    'w-full transition-all duration-300',
                                                    showSidebar ? 'lg:ml-64' : 'lg:ml-0'
                                                ]"
                                            >
                                                <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 shadow-sm backdrop-blur-xl lg:px-6">
                                                    <div class="flex items-center gap-3">
                                                        <button
                                                            type="button"
                                                            @click="showSidebar = !showSidebar"
                                                            class="rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm"
                                                        >
                                                            ☰
                                                        </button>

                        <div>
                            <h2 class="text-base font-black md:text-xl">
                                Listes des étudiants
                                <span class="text-slate-500">({{ filteredEtudiants.length }})</span>
                            </h2>
                            <p class="hidden text-xs text-slate-500 sm:block">
                                Affichage par classe et série
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="showCreate = !showCreate" class="rounded-xl bg-teal-600 px-4 py-2 text-xs font-black text-white shadow-lg shadow-teal-600/20 hover:bg-teal-500">
                        + Ajouter
                    </button>
                </header>

                <section class="p-3 lg:p-5">
                    <div class="mb-3 grid grid-cols-2 gap-2 md:flex md:flex-wrap md:items-center md:justify-end">
                        <button type="button" @click="refreshPage" class="btn-soft teal">
                            ⟳ Actualiser
                        </button>

                        <div class="col-span-2 flex gap-2 md:col-span-1">

                            <select
                                v-model="selectedAnneeFilter"
                                @change="filterByAnnee"
                                class="filter-select"
                            >
                                <option value="">
                                    Toutes les années scolaires
                                </option>

                                <option
                                    v-for="annee in anneeScolaires"
                                    :key="annee.id"
                                    :value="annee.annee || annee.nom"
                                >
                                    {{ annee.annee || annee.nom }}
                                    {{ annee.active ? ' - ACTIVE' : '' }}
                                </option>
                            </select>


                                    <select
                                        v-model="selectedClasseFilter"
                                        class="filter-select flex-1"
                                    >
                                        <option value="">
                                            Toutes les classes
                                        </option>

                                        <option
                                            v-for="classe in activeClasses"
                                            :key="classe.id"
                                            :value="String(classe.id)"
                                        >
                                            {{ classe.nom_classe }}
                                        </option>
                                    </select>

                            <select
                            v-model="selectedSerieFilter"
                            class="filter-select flex-1"
                            :disabled="!selectedClasseFilter"
                        >
                            <option value="">
                                Toutes les séries
                            </option>

                            <option
                                v-for="serie in filterSeries.filter(serie =>
                                    (serie.annee_scolaire || '') === props.anneeActive
                                )"
                                :key="serie.id"
                                :value="String(serie.id)"
                            >
                                Série {{ serie.nom_serie }}
                            </option>
                        </select>
                        </div>

                        <button type="button" @click="exportExcel" class="btn-soft green">
                            Export Excel
                        </button>

                        <button type="button" @click="printPdf" class="btn-soft red">
                            Imprimer PDF
                        </button>
                    </div>

                    <div class="mb-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div class="flex flex-1 flex-col gap-2 sm:flex-row">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Rechercher un étudiant..."
                                    class="h-9 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs outline-none focus:border-teal-500 focus:bg-white"
                                />

                                <button type="button" class="btn-main blue">🔍 Rechercher</button>
                                <button type="button" @click="search = ''" class="btn-main red">✕ Initialiser</button>
                            </div>
                        </div>
                    </div>

                    <div v-if="showCreate" class="mb-4 rounded-[24px] border border-slate-200 bg-white p-4 shadow-xl shadow-slate-200/60">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-black">Ajouter un étudiant</h3>
                                <p class="text-xs text-slate-500">
                                    Nom, prénoms, sexe, classe, téléphone et adresse sont obligatoires.
                                </p>
                            </div>

                            <button type="button" @click="showCreate = false" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold">
                                ✕
                            </button>
                        </div>

                        <div class="grid gap-2 md:grid-cols-4">
                            <div>
                                <input v-model="form.nom" placeholder="Nom *" class="input-pro" />
                                <p v-if="form.errors.nom" class="error">{{ form.errors.nom }}</p>
                            </div>

                            <div>
                                <input v-model="form.prenoms" placeholder="Prénom(s) *" class="input-pro" />
                                <p v-if="form.errors.prenoms" class="error">{{ form.errors.prenoms }}</p>
                            </div>

                            <div>
                                <select v-model="form.sexe" class="input-pro">
                                    <option disabled value="">Sexe *</option>
                                    <option value="Masculin">Masculin</option>
                                    <option value="Féminin">Féminin</option>
                                </select>
                                <p v-if="form.errors.sexe" class="error">{{ form.errors.sexe }}</p>
                            </div>

                            <div>
                                <select v-model="form.classe_id" class="input-pro">
                                    <option disabled value="">Classe *</option>
                                    <option v-for="classe in classes" :key="classe.id" :value="String(classe.id)">
                                        {{ classe.nom_classe }} - {{ classe.niveau?.nom_niveau || 'Sans niveau' }}
                                    </option>
                                </select>
                                <p v-if="form.errors.classe_id" class="error">{{ form.errors.classe_id }}</p>
                            </div>

                            <div>
                                <select v-model="form.serie_id" class="input-pro" :disabled="!form.classe_id">
                                    <option value="">Série</option>
                                    <option v-for="serie in formSeries" :key="serie.id" :value="String(serie.id)">
                                        Série {{ serie.nom_serie }}
                                    </option>
                                </select>
                            </div>

                            <input v-model="form.email" placeholder="Email" class="input-pro" />

                            <div>
                                <input v-model="form.telephone" placeholder="Téléphone *" class="input-pro" />
                                <p v-if="form.errors.telephone" class="error">{{ form.errors.telephone }}</p>
                            </div>

                            <div>
                                <input v-model="form.adresse" placeholder="Adresse *" class="input-pro" />
                                <p v-if="form.errors.adresse" class="error">{{ form.errors.adresse }}</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="submit"
                            :disabled="form.processing || classes.length === 0"
                            class="mt-3 rounded-2xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white shadow-lg hover:bg-emerald-500 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Enregistrement...' : '💾 Enregistrer' }}
                        </button>
                    </div>

                    <div class="hidden overflow-visible rounded-[24px] border border-slate-200 bg-white shadow-xl shadow-slate-200/60 md:block">
                        <div class="overflow-x-auto overflow-y-visible">
                            <table class="w-full min-w-[1350px] text-left text-xs compact-table">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] uppercase text-slate-500">

                                        <th class="px-3 py-2">Matricule</th>
                                        <th class="px-3 py-2">Site</th>
                                        <th class="px-3 py-2">AS</th>
                                        <th class="px-3 py-2">Date inscription</th>
                                        <th class="px-3 py-2">Nom</th>
                                        <th class="px-3 py-2">Prénom(s)</th>
                                        <th class="px-3 py-2">Sexe</th>
                                        <th class="px-3 py-2">Classe</th>
                                        <th class="px-3 py-2">Niveau</th>
                                        <th class="px-3 py-2">Série</th>
                                        <th class="px-3 py-2">Contact</th>
                                        <th class="px-3 py-2">Date Naiss.</th>
                                        <th class="px-3 py-2">Lieu Naiss.</th>
                                        <th class="px-3 py-2 text-right">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="e in filteredEtudiants" :key="e.id" class="border-b border-slate-100 transition hover:bg-teal-50/40 leading-tight">
                                        <td class="px-3 py-2">
                                            <span
                                                class="rounded-lg bg-blue-50 px-2 py-1 text-[11px] font-black text-blue-700"
                                            >
                                                {{
                                                    e.numero
                                                    || e.numero_matricule
                                                    || e.inscription?.numero_matricule
                                                    || '-'
                                                }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-2">{{ e.site || 'Strelitzia School' }}</td>
                                        <td class="px-3 py-2">{{ e.annee_scolaire || '2025-2026' }}</td>
                                        <td class="px-3 py-2">{{ getDateInscription(e) }}</td>
                                        <td class="px-3 py-2 font-bold">{{ e.nom || '-' }}</td>
                                        <td class="px-3 py-2">{{ e.prenoms || '-' }}</td>

                                        <td class="px-3 py-2">
                                            <span
                                                :class="[
                                                    'rounded-full px-2 py-1 text-[10px] font-bold',
                                                    e.sexe === 'Masculin'
                                                        ? 'bg-blue-50 text-blue-600'
                                                        : 'bg-pink-50 text-pink-600'
                                                ]"
                                            >
                                                {{ e.sexe || 'Non défini' }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-2 font-semibold">{{ getClasse(e) }}</td>
                                        <td class="px-3 py-2">{{ getNiveau(e) }}</td>
                                        <td class="px-3 py-2">{{ getSerie(e) }}</td>
                                        <td class="px-3 py-2">{{ e.telephone || e.contact || 'Non renseigné' }}</td>
                                        <td class="px-3 py-2">{{ e.date_naissance || '-' }}</td>
                                        <td class="px-3 py-2">{{ e.lieu_naissance || '-' }}</td>

                                        <td class="relative px-3 py-2 text-right">
                                            <button type="button" @click="toggleActions(e.id)" class="action-menu-btn">
                                                Actions ▾
                                            </button>

                                            <div v-if="activeActionId === e.id" class="action-menu-box">
                                                <button type="button" @click="openInfo(e)" class="action-menu-item teal">
                                                    Info étudiant
                                                </button>

                                                <button type="button" @click="showParents(e)" class="action-menu-item blue">
                                                    Parents
                                                </button>

                                                <button type="button" @click="openEdit(e)" class="action-menu-item amber">
                                                    ✏️ Modifier
                                                </button>

                                                <button type="button" @click="remove(e.id)" class="action-menu-item rose">
                                                    🗑️ Supprimer
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-if="filteredEtudiants.length === 0">
                                        <td colspan="14" class="py-10 text-center text-slate-400">
                                            Aucun étudiant trouvé.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>
        </div>

                   <!-- INFORMATION ETUDIANT AVANT VISUALISATION PDF -->

        <div
            v-if="selectedInfo"
            class="fixed inset-0 z-[130] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="closeInfo"
        >
            <div class="flex max-h-[95vh] w-full max-w-4xl flex-col overflow-hidden rounded-[30px] bg-white shadow-2xl">
                <div class="flex items-center justify-between bg-gradient-to-r from-teal-700 to-slate-900 p-5 text-white">
                    <div>
                        <h2 class="text-xl font-black">Fiche information étudiant</h2>
                        <p class="text-xs text-white/70">Visualisation avant impression PDF</p>
                    </div>

                    <button type="button" @click="closeInfo" class="rounded-xl bg-white/10 px-3 py-2 font-bold hover:bg-white/20">
                        ✕
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <div class="mb-5 flex flex-col gap-4 rounded-3xl bg-slate-50 p-4 md:flex-row md:items-center">
                        <img
                            v-if="getPhoto(selectedInfo)"
                            :src="getPhoto(selectedInfo)"
                            class="h-28 w-28 rounded-3xl border-4 border-white object-cover shadow-xl"
                        />

                        <div v-else class="grid h-28 w-28 place-items-center rounded-3xl bg-slate-200 text-4xl shadow-xl">
                            👤
                        </div>

                        <div class="flex-1">
                            <h3 class="text-2xl font-black text-slate-900">
                                {{ selectedInfo.nom || '-' }} {{ selectedInfo.prenoms || '' }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Matricule : <b>{{ selectedInfo.numero || '-' }}</b>
                            </p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="info-badge">Classe : {{ getClasse(selectedInfo) }}</span>
                                <span class="info-badge">Niveau : {{ getNiveau(selectedInfo) }}</span>
                                <span class="info-badge">Série : {{ getSerie(selectedInfo) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="info-card">
                            <h4>Informations personnelles</h4>
                            <p><b>Sexe :</b> {{ selectedInfo.sexe || '-' }}</p>
                            <p><b>Date inscription :</b> {{ getDateInscription(selectedInfo) }}</p>
                            <p><b>Date naissance :</b> {{ selectedInfo.date_naissance || '-' }}</p>
                            <p><b>Lieu naissance :</b> {{ selectedInfo.lieu_naissance || '-' }}</p>
                        </div>

                        <div class="info-card">
                            <h4>Contact</h4>
                            <p><b>Téléphone :</b> {{ selectedInfo.telephone || selectedInfo.contact || '-' }}</p>
                            <p><b>Email :</b> {{ selectedInfo.email || '-' }}</p>
                            <p><b>Adresse :</b> {{ selectedInfo.adresse || '-' }}</p>
                            <p><b>Site :</b> {{ selectedInfo.site || 'Strelitzia School' }}</p>
                        </div>

                        <div class="info-card md:col-span-2">
                            <h4>Parents / Tuteur</h4>
                            <div class="grid gap-2 md:grid-cols-3">
                                <p><b>Père :</b> {{ selectedInfo.inscription?.pere_nom || '-' }}</p>
                                <p><b>Tél. père :</b> {{ selectedInfo.inscription?.pere_telephone || '-' }}</p>
                                <p><b>Profession :</b> {{ selectedInfo.inscription?.pere_profession || '-' }}</p>
                                <p><b>Mère :</b> {{ selectedInfo.inscription?.mere_nom || '-' }}</p>
                                <p><b>Tél. mère :</b> {{ selectedInfo.inscription?.mere_telephone || '-' }}</p>
                                <p><b>Profession :</b> {{ selectedInfo.inscription?.mere_profession || '-' }}</p>
                                <p><b>Tuteur :</b> {{ selectedInfo.inscription?.tuteur_nom || '-' }}</p>
                                <p><b>Tél. tuteur :</b> {{ selectedInfo.inscription?.tuteur_telephone || '-' }}</p>
                                <p><b>Lien :</b> {{ selectedInfo.inscription?.tuteur_lien || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="sticky bottom-0 mt-6 flex flex-col gap-3 border-t bg-white pt-4 sm:flex-row sm:justify-end">
                        <button type="button" @click="printStudentPdf(selectedInfo)" class="w-full rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-black text-white hover:bg-blue-700 sm:w-auto">
                            📄 Visualiser / PDF
                        </button>

                        <button type="button" @click="closeInfo" class="w-full rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white hover:bg-slate-800 sm:w-auto">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

       <div
    v-if="showModal"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
    @click.self="closeModal"
>
    <div class="max-h-[95vh] w-full max-w-2xl overflow-y-auto rounded-[30px] bg-white shadow-2xl">

        <!-- HEADER -->
        <div class="sticky top-0 z-20 flex items-center justify-between bg-gradient-to-r from-amber-500 to-slate-900 p-5 text-white">
            <div>
                <h2 class="text-xl font-black">
                    Modifier étudiant
                </h2>

                <p class="text-xs text-white/70">
                    Modification des informations principales
                </p>
            </div>

            <button
                type="button"
                @click="closeModal"
                class="rounded-xl bg-white/10 px-3 py-2 font-bold hover:bg-white/20"
            >
                ✕
            </button>
        </div>

        <!-- BODY -->
        <div class="max-h-[75vh] overflow-y-auto p-6">

            <!-- INFOS -->
            <div
                v-if="selected"
                class="mb-5 rounded-3xl bg-slate-50 p-4"
            >
                <p class="text-xs font-bold uppercase text-slate-400">
                    Étudiant sélectionné
                </p>

                <h3 class="text-lg font-black text-slate-900">
                    {{ selected.nom }} {{ selected.prenoms }}
                </h3>

                <p class="text-xs text-slate-500">
                    {{ getClasse(selected) }}
                    -
                    {{ getNiveau(selected) }}
                    -
                    Série {{ getSerie(selected) }}
                </p>
            </div>

            <!-- FORM -->
            <div class="grid gap-3 md:grid-cols-2">

                <div>
                    <label class="label-pro">Nom *</label>

                    <input
                        v-model="editForm.nom"
                        placeholder="Nom *"
                        class="input-pro"
                    />

                    <p
                        v-if="editForm.errors.nom"
                        class="error"
                    >
                        {{ editForm.errors.nom }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">Prénom(s) *</label>

                    <input
                        v-model="editForm.prenoms"
                        placeholder="Prénom(s) *"
                        class="input-pro"
                    />

                    <p
                        v-if="editForm.errors.prenoms"
                        class="error"
                    >
                        {{ editForm.errors.prenoms }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">Sexe *</label>

                    <select
                        v-model="editForm.sexe"
                        class="input-pro"
                    >
                        <option value="">
                            Sexe *
                        </option>

                        <option value="Masculin">
                            Masculin
                        </option>

                        <option value="Féminin">
                            Féminin
                        </option>
                    </select>

                    <p
                        v-if="editForm.errors.sexe"
                        class="error"
                    >
                        {{ editForm.errors.sexe }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">
                        Date de naissance
                    </label>

                    <input
                        v-model="editForm.date_naissance"
                        type="date"
                        class="input-pro"
                    />
                </div>

                <div>
                    <label class="label-pro">
                        Lieu de naissance
                    </label>

                    <input
                        v-model="editForm.lieu_naissance"
                        placeholder="Lieu de naissance"
                        class="input-pro"
                    />
                </div>

                <div>
                    <label class="label-pro">Classe *</label>

                    <select
                        v-model="editForm.classe_id"
                        class="input-pro"
                    >
                        <option value="">
                            Classe *
                        </option>

                        <option
                            v-for="classe in classes"
                            :key="classe.id"
                            :value="String(classe.id)"
                        >
                            {{ classe.nom_classe }}
                            -
                            {{ classe.niveau?.nom_niveau || 'Sans niveau' }}
                        </option>
                    </select>

                    <p
                        v-if="editForm.errors.classe_id"
                        class="error"
                    >
                        {{ editForm.errors.classe_id }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">
                        Série
                    </label>

                    <select
                        v-model="editForm.serie_id"
                        class="input-pro"
                        :disabled="!editForm.classe_id"
                    >
                        <option value="">
                            Série
                        </option>

                        <option
                            v-for="serie in editSeries"
                            :key="serie.id"
                            :value="String(serie.id)"
                        >
                            Série {{ serie.nom_serie }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="label-pro">
                        Email
                    </label>

                    <input
                        v-model="editForm.email"
                        placeholder="Email"
                        class="input-pro"
                    />
                </div>

                <div>
                    <label class="label-pro">
                        Téléphone *
                    </label>

                    <input
                        v-model="editForm.telephone"
                        placeholder="Téléphone *"
                        class="input-pro"
                    />

                    <p
                        v-if="editForm.errors.telephone"
                        class="error"
                    >
                        {{ editForm.errors.telephone }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">
                        Adresse *
                    </label>

                    <input
                        v-model="editForm.adresse"
                        placeholder="Adresse *"
                        class="input-pro"
                    />

                    <p
                        v-if="editForm.errors.adresse"
                        class="error"
                    >
                        {{ editForm.errors.adresse }}
                    </p>
                </div>

                <!-- PHOTO -->
                <div class="md:col-span-2">

                    <label class="label-pro">
                        Photo de l'étudiant
                    </label>

                    <div class="mb-3 rounded-2xl bg-slate-50 p-4">

                        <div class="mb-4 flex items-center gap-4">

                            <img
                                v-if="editForm.photo"
                                :src="URL.createObjectURL(editForm.photo)"
                                class="h-24 w-24 rounded-2xl border object-cover"
                            />

                            <img
                                v-else-if="selected && getPhoto(selected)"
                                :src="getPhoto(selected)"
                                class="h-24 w-24 rounded-2xl border object-cover"
                            />

                            <div
                                v-else
                                class="grid h-24 w-24 place-items-center rounded-2xl bg-slate-200 text-4xl"
                            >
                                👤
                            </div>

                            <div class="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleEditPhoto"
                                    class="input-pro"
                                />

                                <p class="mt-2 text-xs text-slate-400">
                                    JPG, PNG — max 4MB
                                </p>
                            </div>

                        </div>

                        <button
                            type="button"
                            @click="update"
                            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700"
                        >
                            ✅ Valider la photo
                        </button>

                    </div>

                    <p
                        v-if="editForm.errors.photo"
                        class="error"
                    >
                        {{ editForm.errors.photo }}
                    </p>

                </div>

            </div>

            <!-- ACTIONS -->
            <div class="sticky bottom-0 mt-6 flex gap-3 bg-white pt-4">

                <button
                    type="button"
                    @click="closeModal"
                    class="flex-1 rounded-2xl bg-slate-100 py-3 text-sm font-black text-slate-700"
                >
                    Annuler
                </button>

                <button
                    type="button"
                    @click="update"
                    :disabled="editForm.processing"
                    class="flex-1 rounded-2xl bg-teal-600 py-3 text-sm font-black text-white disabled:opacity-50"
                >
                    {{
                        editForm.processing
                            ? 'Sauvegarde...'
                            : 'Sauvegarder'
                    }}
                </button>

            </div>

        </div>
    </div>
</div>


        <div
            v-if="selectedParents"
            class="fixed inset-0 z-[120] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="closeParents"
        >
            <div class="w-full max-w-3xl overflow-hidden rounded-[30px] bg-white shadow-2xl">
                <div class="flex items-center justify-between bg-gradient-to-r from-blue-700 to-slate-900 p-5 text-white">
                    <div>
                        <h2 class="text-xl font-black">Modifier parents & tuteur</h2>
                        <p class="text-xs text-white/70">Informations responsables de l’étudiant</p>
                    </div>

                    <button type="button" @click="closeParents" class="rounded-xl bg-white/10 px-3 py-2 font-bold hover:bg-white/20">
                        ✕
                    </button>
                </div>

                <div class="p-6">
                    <div class="mb-5 rounded-3xl bg-blue-50 p-4 text-sm text-blue-900">
                        <p><b>Étudiant :</b> {{ selectedParents.nom || '-' }} {{ selectedParents.prenoms || '' }}</p>
                        <p>
                            <b>Classe :</b> {{ getClasse(selectedParents) }}
                            — <b>Niveau :</b> {{ getNiveau(selectedParents) }}
                            — <b>Série :</b> {{ getSerie(selectedParents) }}
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="parent-card">
                            <h3>👨 Père</h3>
                            <input v-model="parentForm.pere_nom" class="input-pro" placeholder="Nom du père" />
                            <input v-model="parentForm.pere_telephone" class="input-pro" placeholder="Téléphone du père" />
                            <input v-model="parentForm.pere_profession" class="input-pro" placeholder="Profession du père" />
                        </div>

                        <div class="parent-card">
                            <h3>👩 Mère</h3>
                            <input v-model="parentForm.mere_nom" class="input-pro" placeholder="Nom de la mère" />
                            <input v-model="parentForm.mere_telephone" class="input-pro" placeholder="Téléphone de la mère" />
                            <input v-model="parentForm.mere_profession" class="input-pro" placeholder="Profession de la mère" />
                        </div>

                        <div class="parent-card">
                            <h3>👤 Tuteur</h3>
                            <input v-model="parentForm.tuteur_nom" class="input-pro" placeholder="Nom du tuteur" />
                            <input v-model="parentForm.tuteur_telephone" class="input-pro" placeholder="Téléphone du tuteur" />
                            <input v-model="parentForm.tuteur_lien" class="input-pro" placeholder="Lien avec l’étudiant" />
                            <input v-model="parentForm.tuteur_adresse" class="input-pro" placeholder="Adresse du tuteur" />
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="closeParents" class="flex-1 rounded-2xl bg-slate-100 py-3 text-sm font-black text-slate-700">
                            Annuler
                        </button>

                        <button type="button" @click="updateParents" :disabled="parentForm.processing" class="flex-1 rounded-2xl bg-blue-600 py-3 text-sm font-black text-white disabled:opacity-50">
                            {{ parentForm.processing ? 'Sauvegarde...' : 'Sauvegarder parents' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.menu-link {
    display: block;
    border-radius: 16px;
    padding: 12px 16px;
    color: rgb(203 213 225);
    transition: 0.2s;
}

.menu-link:hover {
    background: rgba(255,255,255,0.08);
    color: white;
}

.submenu {
    display: block;
    margin-left: 12px;
    border-radius: 14px;
    padding: 10px 16px;
    color: rgb(203 213 225);
    font-size: 13px;
}

.submenu.active,
.submenu:hover {
    background: rgba(255,255,255,0.10);
    color: white;
}

.btn-soft {
    height: 34px;
    border-radius: 12px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 900;
    border: 1px solid rgb(226 232 240);
    background: white;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
}

.btn-soft.teal { color: #0f766e; border-color: #99f6e4; }
.btn-soft.green { color: #15803d; border-color: #bbf7d0; }
.btn-soft.red { color: #dc2626; border-color: #fecaca; }

.filter-select {
    height: 34px;
    border-radius: 12px;
    border: 1px solid rgb(226 232 240);
    background: white;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 800;
    outline: none;
    min-width: 0;
}

.filter-select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-main {
    height: 36px;
    border-radius: 12px;
    padding: 0 13px;
    font-size: 12px;
    font-weight: 900;
    color: white;
    white-space: nowrap;
}

.btn-main.blue { background: #2563eb; }
.btn-main.red { background: #ef4444; }

.input-pro {
    width: 100%;
    height: 38px;
    border-radius: 14px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 0 12px;
    font-size: 13px;
    outline: none;
}

.input-pro:focus {
    border-color: #14b8a6;
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.12);
}

.input-pro:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.label-pro {
    margin-bottom: 5px;
    display: block;
    font-size: 11px;
    font-weight: 900;
    color: #64748b;
    text-transform: uppercase;
}

.error {
    margin-top: 3px;
    font-size: 11px;
    color: #ef4444;
    font-weight: 700;
}

.compact-table th,
.compact-table td {
    line-height: 1.12;
    white-space: nowrap;
}

.compact-table tbody tr {
    height: 38px;
}

.action-menu-btn {
    border-radius: 12px;
    background: #0f766e;
    padding: 7px 12px;
    font-size: 11px;
    font-weight: 900;
    color: white;
    box-shadow: 0 8px 18px rgba(15, 118, 110, 0.2);
}

.action-menu-box {
    position: absolute;
    right: 12px;
    top: 34px;
    z-index: 999;
    width: 130px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: white;
    padding: 5px;
    box-shadow: 0 18px 35px rgba(15, 23, 42, 0.18);
}

.action-menu-item {
    display: block;
    width: 100%;
    border-radius: 10px;
    padding: 7px 8px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 900;
    margin-bottom: 4px;
    line-height: 1.1;
}

.action-menu-item:last-child {
    margin-bottom: 0;
}

.action-menu-item.teal { background: #ccfbf1; color: #0f766e; }
.action-menu-item.blue { background: #dbeafe; color: #1d4ed8; }
.action-menu-item.amber { background: #fef3c7; color: #b45309; }
.action-menu-item.rose { background: #ffe4e6; color: #be123c; }

.info-badge {
    border-radius: 999px;
    background: #ccfbf1;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 900;
    color: #0f766e;
}

.info-card {
    border-radius: 22px;
    border: 1px solid rgb(226 232 240);
    background: white;
    padding: 16px;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.04);
}

.info-card h4 {
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: 900;
    color: #0f172a;
}

.info-card p {
    margin-bottom: 6px;
    font-size: 13px;
    color: #334155;
}

.parent-card {
    border-radius: 22px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 14px;
}

.parent-card h3 {
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: 900;
    color: #0f172a;
}

.parent-card .input-pro {
    margin-bottom: 8px;
}

.page-btn {
    height: 30px;
    min-width: 30px;
    border-radius: 12px;
    background: rgb(248 250 252);
    font-weight: 900;
}

.page-btn.active {
    background: #0d9488;
    color: white;
}

@media (max-width: 768px) {
    .btn-soft {
        width: 100%;
    }

    .filter-select {
        width: 100%;
    }
}
/* SCROLLBAR SIDEBAR */

aside::-webkit-scrollbar {
    width: 6px;
}

aside::-webkit-scrollbar-track {
    background: transparent;
}

aside::-webkit-scrollbar-thumb {
    background: #14b8a6;
    border-radius: 999px;
}

aside::-webkit-scrollbar-thumb:hover {
    background: #0d9488;
}
</style>
