"use client";

import { useEffect, useState } from "react";

export default function Home() {
  const [basicPrice, setBasicPrice] = useState("");
  const [proPrice, setProPrice] = useState("");

  useEffect(() => {
    fetch("/api/config")
      .then((r) => r.json())
      .then(({ basicPrice, proPrice }) => {
        setBasicPrice(basicPrice);
        setProPrice(proPrice);
      });
  }, []);

  return (
    <main className="flex min-h-screen flex-col items-center justify-center p-8">
      <div className="w-full max-w-2xl">
        <h1 className="text-3xl font-bold text-center mb-8">
          Choose a collaboration plan
        </h1>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Basic Plan */}
          <div className="bg-white p-8 rounded-lg shadow-md text-center">
            <form action="/api/create-checkout-session" method="POST">
              <input type="hidden" name="priceId" value={basicPrice} />
              <div className="text-6xl mb-4">🚀</div>
              <div className="text-xl font-semibold mb-2">Starter</div>
              <div className="text-4xl font-bold mb-1">$12</div>
              <div className="text-gray-500 mb-6">per month</div>
              <button
                type="submit"
                className="w-full bg-stripe-purple text-white py-3 rounded-md font-semibold hover:bg-opacity-90 transition-colors"
              >
                Select
              </button>
            </form>
          </div>

          {/* Pro Plan */}
          <div className="bg-white p-8 rounded-lg shadow-md text-center">
            <form action="/api/create-checkout-session" method="POST">
              <input type="hidden" name="priceId" value={proPrice} />
              <div className="text-6xl mb-4">⚡</div>
              <div className="text-xl font-semibold mb-2">Professional</div>
              <div className="text-4xl font-bold mb-1">$18</div>
              <div className="text-gray-500 mb-6">per month</div>
              <button
                type="submit"
                className="w-full bg-stripe-purple text-white py-3 rounded-md font-semibold hover:bg-opacity-90 transition-colors"
              >
                Select
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  );
}
