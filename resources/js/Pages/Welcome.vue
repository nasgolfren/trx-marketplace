<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive, ref, computed, onMounted  } from 'vue';
import Menubar from 'primevue/menubar';
import Avatar from 'primevue/avatar';
import Dialog from 'primevue/dialog';
import Panel from 'primevue/panel';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import SplitButton from 'primevue/splitbutton';
import Fieldset from 'primevue/fieldset';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Divider from 'primevue/divider';
import Image from 'primevue/image';
import { router } from '@inertiajs/vue3';
import { convertJsonToReadableText } from '@/functions';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';                   // optional


// App control params
const toast = useToast();
const showWalletDetails = ref(false);
const walletLoaded = ref(false);

// tron-wallet params
const tronWallet = ref();

const walletAddress = ref();
const walletBalance = ref();
const walletName = ref();
const walletBandwidth = ref();
const walletEnergy = ref();


const sources = ref([
    { name: 'Energy', code: 'energy' },
    { name: 'Brandwidth', code: 'brandwidth' },
]);

const formOrder = useForm({
    amount: 1000,
    days: 3,
    price: 50,
    address: '',
    selectedSource: {
        name: 'Energy',
        code: 'energy',
    },
})

defineProps({
    orders: Array,
});

const buttonitems = [
    {
        label: 'Show details',
        icon: 'pi pi-id-card',
        command: () => {
            showWalletDetails.value = true;
        }
    },
    {
        label: 'Disconnect',
        icon: 'pi pi-power-off',
        command: () => {
            tronWallet.value = null;
            walletLoaded.value = false;
        }
    },
];

const initTrxWallet = () => {
    setTimeout(async function () {
        __initWallet()
        .then(function(response) {
            __showTronInfo()
        })
        .catch(function (error) {
            console.log(error);
            toast.add({ severity: 'error', summary: 'Error', detail: error, life: 3000 });
        });
    }, 500);
}

async function __initWallet() {
    if (window.tronLink.ready) {
        tronWeb = tronLink.tronWeb;
    } else {
        const res = await tronLink.request({ method: 'tron_requestAccounts' });
        if (res.code === 200) {
            tronWeb = tronLink.tronWeb;
        }
    }

    tronWallet.value = tronWeb;
}

async function __showTronInfo() {
    try {
        let localWalletObj = tronWallet.value;

        walletLoaded.value = true;

        walletAddress.value = await localWalletObj.address.fromHex((await localWalletObj.trx.getAccount()).address);
        walletName.value = localWalletObj.defaultAddress.name;
        walletBalance.value = (await localWalletObj.trx.getBalance(walletAddress.value)) / (10 ** 6);
        walletBandwidth.value = (await localWalletObj.trx.getBandwidth(walletAddress.value));
        walletEnergy.value = (await localWalletObj.trx.getBandwidth(walletAddress.value));

    } catch (error) {
        console.log('error: ', error);

        toast.add({ severity: 'error', summary: 'Error', detail: error, life: 3000 });
        walletLoaded.value = false;
    }
}

async function copyTrxWallet() {
    try {
        await navigator.clipboard.writeText(walletAddress.value);
        toast.add({ severity: 'info', summary: 'Info', detail: 'Address was copied to clipboard', life: 3000 });
    } catch (error) {
        console.log('error: ', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Copy process interupted', life: 3000 });
    }
}

const updateDaysAmount = (event) => {
    formOrder.days = event.value;
};

const updateAmount = (event) => {
    formOrder.amount = event.value;
};
const updatePrice = (event) => {
    formOrder.price = event.value;
};

let validationTimeout = null;
const tronAddressValidity = () => {
    if (formOrder.address == '' || formOrder.address.length < 34) {
        return;
    }

    delete formOrder.errors.address;

    const options = {
        method: 'POST',
        headers: { accept: 'application/json', 'content-type': 'application/json' },
        body: JSON.stringify({ address: formOrder.address, visible: true })
    };

    // if (validationTimeout) {
    //     clearTimeout(validationTimeout);
    // }

    //validationTimeout = setTimeout(() => {
        fetch('https://api.shasta.trongrid.io/wallet/validateaddress', options)
            .then(response => response.json())
            .then(function (response) {
                if (response.result == false) {
                    formOrder.errors.address = 'Tron address is not valid';
                    //toast.add({ severity: 'error', summary: 'Error', detail: 'Tron address is not valid', life: 3000 });
                    return;
                }

                //toast.add({ severity: 'info', summary: 'Info', detail: 'Tron address is OK', life: 3000 });
            })
            .catch(function (error) {

                formOrder.errors.address = 'Something goes wrong, try it again or contact admin!';

                //toast.add({ severity: 'error', summary: 'Error', detail: error, life: 3000 });
            });
    //}, 1000);
};

const finalPrice = computed(() => {
  let result = formOrder.amount * formOrder.price * formOrder.days;
  return result ? result / 1000000 : 0;
});

const formOrderSubmit = () => {
    formOrder.transform((data) => ({
        ...data,
        'source': data.selectedSource.code,
    }))
    .post('/orders', {
        errorBag: 'submitOrder',
        preserveState: true,
        preserveScroll: true,
        onSuccess: (response) => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'The order has been successfully created!', life: 3000 });
        },
        onError: (errors) => {
            toast.add({ severity: 'error', summary: 'Error', detail: convertJsonToReadableText(errors), life: 3000 });
            console.log("error", convertJsonToReadableText(errors));
        }
    });
};

const isSubmitButtonDisabled = computed(() => {
    return Object.keys(formOrder.errors).length >= 1;
});
</script>

<template>

    <Head title="Welcomeeeee" />

    <div class="container mx-auto">
        <Menubar class="my-8">
            <template #start>
                <svg width="35" height="40" viewBox="0 0 35 40" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="h-8">
                    <path
                        d="M25.87 18.05L23.16 17.45L25.27 20.46V29.78L32.49 23.76V13.53L29.18 14.73L25.87 18.04V18.05ZM25.27 35.49L29.18 31.58V27.67L25.27 30.98V35.49ZM20.16 17.14H20.03H20.17H20.16ZM30.1 5.19L34.89 4.81L33.08 12.33L24.1 15.67L30.08 5.2L30.1 5.19ZM5.72 14.74L2.41 13.54V23.77L9.63 29.79V20.47L11.74 17.46L9.03 18.06L5.72 14.75V14.74ZM9.63 30.98L5.72 27.67V31.58L9.63 35.49V30.98ZM4.8 5.2L10.78 15.67L1.81 12.33L0 4.81L4.79 5.19L4.8 5.2ZM24.37 21.05V34.59L22.56 37.29L20.46 39.4H14.44L12.34 37.29L10.53 34.59V21.05L12.42 18.23L17.45 26.8L22.48 18.23L24.37 21.05ZM22.85 0L22.57 0.69L17.45 13.08L12.33 0.69L12.05 0H22.85Z"
                        class="fill-primary-500 dark:fill-primary-400" />
                    <path
                        d="M30.69 4.21L24.37 4.81L22.57 0.69L22.86 0H26.48L30.69 4.21ZM23.75 5.67L22.66 3.08L18.05 14.24V17.14H19.7H20.03H20.16H20.2L24.1 15.7L30.11 5.19L23.75 5.67ZM4.21002 4.21L10.53 4.81L12.33 0.69L12.05 0H8.43002L4.22002 4.21H4.21002ZM21.9 17.4L20.6 18.2H14.3L13 17.4L12.4 18.2L12.42 18.23L17.45 26.8L22.48 18.23L22.5 18.2L21.9 17.4ZM4.79002 5.19L10.8 15.7L14.7 17.14H14.74H15.2H16.85V14.24L12.24 3.09L11.15 5.68L4.79002 5.2V5.19Z"
                        class="fill-surface-700 dark:fill-surface-0/80" />
                </svg>
            </template>
            <template #end>
                <div class="flex items-center gap-2">
                    <Button v-if="!walletLoaded" @click="initTrxWallet()" size="small" outlined>
                        <Image src="/images/tronlink_icon.png" alt="Image" width="20px" />
                        <span class="px-3 font-semibold">Connect wallet</span>
                    </Button>
                    <SplitButton v-else :model="buttonitems" @click="copyTrxWallet()" size="small">
                        <span class="pi pi-copy"></span>
                        <span class="ml-2 flex items-center font-bold">{{ walletAddress }}</span>
                    </SplitButton>
                </div>

                <Dialog v-model:visible="showWalletDetails" modal header="Wallet details"
                    :style="{ width: 'auto', minWidth: '30rem', padding: '50px' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                    <template #header>
                        <div class="inline-flex items-center justify-center gap-2">
                            <span class="pi pi-wallet mr-2 text-primary-500"></span>
                            <span class="font-bold whitespace-nowrap font-bold font-mono ">Wallet details</span>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 gap-3 content-center ">
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <div class="font-bold ">Wallet name</div>
                            <div class="col-span-2">{{ walletName }}</div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <div class="font-bold">
                                Address
                                <Button @click="copyTrxWallet()" class="py-[2px] inline-flex sm:hidden"
                                    icon="pi pi-copy" text aria-label="copy wallet" />
                            </div>
                            <div class="col-span-2">{{ walletAddress }} <Button @click="copyTrxWallet()"
                                    class="py-[2px] hidden sm:inline-flex" icon="pi pi-copy" text
                                    aria-label="copy wallet" /></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <div class="font-bold">Balance</div>
                            <div class="col-span-2">{{ walletBalance }}</div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <div class="font-bold">Energy</div>
                            <div class="col-span-2">{{ walletEnergy }}</div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <div class="font-bold">Bandwidth</div>
                            <div class="col-span-2">{{ walletBandwidth }}</div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Ok" @click="showWalletDetails = false" text />
                    </template>
                </Dialog>
            </template>
        </Menubar>

        <div class="grid gap-4 grid-cols-3 grid-rows-1">
            <div v-focustrap>
                <form @submit.prevent="formOrderSubmit">
                <Fieldset>
                    <template #legend>
                        <div class="flex items-center gap-2 px-2">
                            <span class="pi pi-shopping-cart mr-2 text-primary-500"></span>
                            <span class="font-bold">Order Form</span>
                        </div>
                    </template>
                    <div class="flex flex-col">
                        <div class="w-full">
                            <label class="block text-xs font-medium text-gray-900">
                                Buy amount <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The amount expected in energy or bandwidth'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputNumber v-model="formOrder.amount" @input="updateAmount" class="w-full" inputId="integeronly" showButtons :min="0" :step="1000" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.amount }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Resource <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The resource type for the order'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <Dropdown v-model="formOrder.selectedSource" :options="sources" class="w-full" optionLabel="name" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.source }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Duration <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The duration of the rented resource in days'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputNumber v-model="formOrder.days" @input="updateDaysAmount" class="w-full"  inputId="expiry" suffix=" days" showButtons :min="1" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.days }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Price <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The price/day in sun for the expected resource'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputNumber v-model="formOrder.price" @input="updatePrice" class="w-full" inputId="integeronly" showButtons :min="0" :step="10" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.price }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Destination <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The target address of the energy / bandwidth obtained. It cannot be a contract address or any other invalid address'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputText type="text" v-model="formOrder.address" @input="tronAddressValidity" class="w-full" placeholder="address" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.address }}</div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="flex justify-between">
                                <div>{{ formOrder.selectedSource.name }}</div>
                                <div v-if="formOrder.selectedSource.code == 'energy'" class="text-amber-500 font-semibold">
                                    {{ formOrder.amount }} <span class="pi pi-bolt"></span>
                                </div>
                                <div v-else class="text-cyan-500 font-semibold">
                                    {{ formOrder.amount }} <span class="pi pi-arrows-h"></span>
                                </div>
                            </div>
                            <div class="flex justify-between mt-2 font-semibold">
                                <div>
                                    Total <span class="pi pi-question-circle text-primary-500 text-sm" v-tooltip.top="'Total = amount * price * duration'" placeholder="Top"></span>
                                </div>
                                <div>{{ finalPrice }} TRX</div>
                            </div>
                        </div>

                        <Button class="mt-4" type="submit" label="Submit Order" :disabled="formOrder.processing || isSubmitButtonDisabled" />
                    </div>
                </Fieldset>
                </form>
            </div>
            <div class="col-span-2">
                <Fieldset>
                    <template #legend>
                        <div class="flex items-center gap-2 px-2">
                            <span class="pi pi-book mr-2 text-primary-500"></span>
                            <span class="font-bold">Orders History</span>
                        </div>
                    </template>

                    <DataTable :value="orders" tableStyle="min-width: 50rem">
                        <Column field="amount" header="Date"></Column>
                        <Column field="amount" header="Amount"></Column>
                        <Column field="days" header="Days"></Column>
                        <Column field="price" header="Price"></Column>
                        <Column field="source" header="Source"></Column>
                    </DataTable>

                </Fieldset>
            </div>
        </div>

    </div>

    <Toast style="top: 75px;" />
</template>

<style module>
.myinput {
    border-radius: 2rem;
    padding: 1rem 2rem;
    border-width: 2px;
}
</style>
