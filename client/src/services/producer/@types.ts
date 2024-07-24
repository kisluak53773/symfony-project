export interface IProducer {
  id: number;
  title: string;
  country: string;
  address: string;
}

export interface IProducerCreate {
  title: string;
  country: string;
  address: string;
}
