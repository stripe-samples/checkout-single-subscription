using Newtonsoft.Json;

public class CreateCheckoutSessionRequest
{
    [JsonProperty("priceId")]
    public string PriceId { get; set; }
}