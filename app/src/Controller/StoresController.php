<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Client;

class StoresController extends AppController
{
    public function index()
    {
        $stores = $this->Stores
            ->find()
            ->leftJoinWith('Addresses')
            ->select(['Stores.id', 'Stores.name', 'Addresses.postal_code'])
            ->toArray();
        
        // Aplica a máscara de CEP no lado do PHP
        foreach ($stores as &$store) {
            $address = $store->_matchingData['Addresses'];
            $postalCode = $address->postal_code;
            $postalCode = substr($postalCode, 0, 5) . '-' . substr($postalCode, 5);
            $address->postal_code = $postalCode;
        }

        $this->set(compact('stores'));
        $this->viewBuilder()->setOption('serialize', ['stores']);
    }

    public function view($id)
    {
        $store = $this->Stores->get($id, [
            'contain' => ['Addresses']
        ]);
    
        $this->set(compact('store'));
        $this->viewBuilder()->setOption('serialize', ['store']);
    }

    public function add()
    {
        $this->request->allowMethod(['post']);

        $storeData = $this->request->getData();
        $store = $this->Stores->newEntity($storeData);
        
        $addressData = $this->processAddressData($storeData['address']);
        
        // Se não foi possível processar as informações do endereço, retorna erro
        if ($addressData === false) {
            $this->respondWithBadRequestError('CEP not found or invalid.');
            return;
        }

        if ($this->Stores->save($store)) {
            $addressData['store_id'] = $store->id;
            
            // Cria uma nova entidade de endereço com os dados processados
            $address = $this->Stores->Addresses->newEntity($addressData);
            
            if ($this->Stores->Addresses->save($address)) {
                $this->set([
                    'success' => true,
                    'data' => [$store, $address],
                    '_serialize' => ['success', 'data'],
                ]);
            } else {
                $this->Stores->delete($store);
                $this->respondWithBadRequestError('Error registering address.', $address->getErrors());
                return;
            }
        } else {
            $this->respondWithBadRequestError('Error registering store.', $store->getErrors());
            return;
        }
    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'put']);

        $store = $this->Stores->get($id, [
            'contain' => ['Addresses']
        ]);

        $storeData = $this->request->getData();
        
        // Processa e adiciona as informações do endereço
        if (isset($storeData['address'])) {
            $address = $store->address;
            $addressData = $this->processAddressData($storeData['address']);

            // Se não foi possível processar as informações do endereço, retorna erro
            if ($addressData === false) {
                $this->respondWithBadRequestError('CEP not found or invalid.');
                return;
            }

            // Atualiza os dados do endereço
            $this->Stores->Addresses->patchEntity($address, $addressData);
        }

        // Atualiza os dados da loja
        $this->Stores->patchEntity($store, $storeData);

        // Salva a loja com os dados do endereço associados
        if ($this->Stores->save($store, ['associated' => ['Addresses']])) {
            $this->set([
                'success' => true,
                'data' => $store,
                '_serialize' => ['success', 'data'],
            ]);
        } else {
            $this->respondWithBadRequestError('Error registering store.', $store->getErrors());
            return;
        }
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);

        $store = $this->Stores->get($id);

        if ($this->Stores->delete($store)) {
            $this->set([
                'success' => true,
                'message' => 'Store deleted successfully',
                '_serialize' => ['success', 'message'],
            ]);
        } else {
            $this->respondWithBadRequestError('Failed to delete store.');
            return;
        }
    }

    private function processAddressData($addressData)
    {
        // Verifica se o CEP está presente nos dados do endereço
        if (!empty($addressData['postal_code'])) {
            $addressCep = $this->searchCEP($addressData['postal_code']);
            $addressDataCep = json_decode($addressCep, true);
            
            // Se as informações do CEP foram encontradas, adiciona ao $addressData
            if (!empty($addressDataCep['cep'])) {
                $addressDataCepAjust = [
                    'state' => $addressDataCep['uf'],
                    'city' => $addressDataCep['localidade'],
                    'sublocality' => $addressDataCep['bairro'],
                    'street' => $addressDataCep['logradouro']
                ];
                
                $addressData = array_merge($addressData, $addressDataCepAjust);
            } else {
                return false;
            }
        } else {
            return false;
        }
        return $addressData;
    }

    private function searchCEP($postalCode)
    {
        $client = new Client();
       $response = $client->get('https://viacep.com.br/ws/' . $postalCode . '/json/');
        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        } else {
            return $response->withStatus(500);
        }
    }

    public function respondWithBadRequestError($message, $errors = []) {
        $this->set([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            '_serialize' => ['success', 'message', 'errors'],
        ]);
        $this->response = $this->response->withStatus(400);
    }
}
