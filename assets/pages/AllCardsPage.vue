<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllCards, fetchSetCodes } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(true);
const currentPage = ref(1);
const totalPages = ref(1);
const limit = 20;
const setCodes = ref([]);
const selectedSetCode = ref('');

async function loadCards(page = 1) {
    loadingCards.value = true;
    console.log('page : ', page);
    const result = await fetchAllCards(page, limit, selectedSetCode.value);
    cards.value = result.cards;
    totalPages.value = result.totalPages;
    loadingCards.value = false;
}

async function loadSetCodes() {
    setCodes.value = await fetchSetCodes();
}

function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        loadCards(currentPage.value);
    }
}

function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
        loadCards(currentPage.value);
    }
}

function handleSetCodeChange() {
    loadCards(currentPage.value);
}

onMounted(() => {
    loadCards();
    loadSetCodes();
});
</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
        <label for="setCodeSelect">Set Code:</label>
        <select id="setCodeSelect" v-model="selectedSetCode" @change="handleSetCodeChange">
            <option value="">All Sets</option>
            <option v-for="setCode in setCodes" :key="setCode.setCode" :value="setCode.setCode">
                {{ setCode.setCode }}
            </option>
        </select>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
    <div class="pagination">
        <button type="button" @click="prevPage" :disabled="currentPage === 1">Previous</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button type="button" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
    </div>
</template>
