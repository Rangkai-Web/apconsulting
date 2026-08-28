import { describe, expect, it } from "vitest";
import { mapOrderForTest } from "../app/services/orderService";

describe("orderService.mapOrder", () => {
  it("maps order_status/payment_status snake_case fields to camelCase", () => {
    const raw = {
      id: 1,
      order_number: "ORD-TEST-1",
      order_status: "processing",
      payment_status: "paid",
      payment_proof: "http://localhost:8000/uploads/payment-proofs/bukti.jpg",
      payment_rejected_reason: null,
      courier: "jne",
      courier_service: "REG",
      shipping_cost: 15000,
      subtotal: 100000,
      total: 115000,
      tracking_number: null,
      created_at: "2026-08-27T00:00:00.000000Z",
      items: [],
      shipping_address: null,
    };

    const order = mapOrderForTest(raw);

    expect(order.orderStatus).toBe("processing");
    expect(order.paymentStatus).toBe("paid");
    expect(order.paymentProof).toBe(
      "http://localhost:8000/uploads/payment-proofs/bukti.jpg",
    );
    expect(order.paymentRejectedReason).toBeNull();
    expect((order as any).status).toBeUndefined();
    expect((order as any).snapToken).toBeUndefined();
  });
});
