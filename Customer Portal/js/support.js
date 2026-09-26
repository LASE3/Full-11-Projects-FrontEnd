/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Incident Support Cockpit Controller
 * Page-specific logic for ticket switching, live message streaming, WebRTC bridge, and audit exports.
 */

(function () {
  "use strict";

  let currentActiveTicket = "TCK-9482";

  const TICKET_DATABASE = {
    "TCK-9482": {
      id: "TCK-9482",
      title:
        "TCK-9482: Sensor Bank #2 Analog Loop Dropout • Severity 1 (Critical)",
      escalation: "INCIDENT ESCALATION LEVEL 3",
      sla: "SLA MET: 7 MIN TO FIRST RESPONSE",
      isCritical: true,
      logged: "10:14:02 AM MSK",
      dispatch: "10:21:40 AM MSK",
      remaining: "02h 45m REMAINING",
      specialist: "Boris K. (On Mezzanine)",
      assetName: "Gas Chromatography Skid #4",
      assetModel: "VP-GC-9082 • Industrial Gas Purity Unit",
      serial: "VP-2023-8812",
      firmware: "v4.12.0 (Patch 2.1 Applied)",
      placement: "Cherepovets Plant, BF #5,<br />Mezzanine Level 2, Bay C",
      projectName: "PRJ-VP-7721 (BF #5 Automation)",
      projectId: "PRJ-VP-7721",
      interface: "Modbus TCP / PROFINET RT",
      diagramDoc: "DOC-WD-7721-04",
      messages: [
        {
          sender: "Alexey R. Danilov",
          role: "Client - Chief Instrumentation Eng.",
          avatar:
            "https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV",
          time: "10:14 AM MSK",
          text: "At 10:11 AM during the planned furnace temperature ramp-up to 1,350°C, sensor channel GC-02 on Skid #4 stopped transmitting 4-20mA signals to the main SCADA hub. Error code 0x7E (Analog Loop Open). We have switched to secondary backup, but need immediate root-cause diagnostic.",
          attachments: [
            {
              name: "telemetry_dump_skid4_1011.log",
              size: "1.4 MB",
              docId: "DOC-TEL-9482-01",
            },
            { name: "scada_error_screenshot.png", size: "820 KB" },
          ],
        },
        {
          isSystem: true,
          time: "10:15:04 AM MSK",
          text: '<span class="font-semibold text-primary">Automated Incident Classification: Severity 1 (Critical Production Impact).</span> SLA countdown initiated (15 min SLA window). Assigned to Industrial Automation Escalation Pool. Dispatched high-priority SMS alert to On-call Specialist: Denis Sokolov.',
        },
        {
          sender: "Denis Sokolov",
          role: "VOSTOKPRIBOR - Tier-3 Automation Engineer",
          initials: "DS",
          time: "10:21 AM MSK",
          text: "Alexey, good morning. I have reviewed the telemetry dump. The loop resistance spiked to infinity indicating either an open terminal at junction box JB-104 or a thermal overload on the galvanic isolator card. Our on-site field tech Boris K. has been dispatched to Blast Furnace #5 control room with a replacement isolator module. In the meantime, I am running a remote diagnostic script on Gateway VP-GW-09.",
        },
        {
          sender: "Denis Sokolov",
          role: "VOSTOKPRIBOR - Tier-3 Automation Engineer",
          initials: "DS",
          time: "10:38 AM MSK",
          text: '<span class="font-semibold text-primary">Update:</span> Remote handshake re-established with the transmitter microcontroller. Boris is now at the cabinet verifying the terminal screws and power rails. Stand by for live calibration test.',
          isHighlight: true,
        },
      ],
    },
    "TCK-9460": {
      id: "TCK-9460",
      title:
        "TCK-9460: Optical Pyrometer Zero-Drift Recalibration • Severity 3 (Medium)",
      escalation: "PLANNED MAINTENANCE DISPATCH",
      sla: "SLA MET: 24 MIN TO FIRST RESPONSE",
      isCritical: false,
      logged: "08:30:15 AM MSK",
      dispatch: "08:54:10 AM MSK",
      remaining: "01h 15m REMAINING",
      specialist: "Anna Timofeeva (Calibration Lead)",
      assetName: "Continuous Casting Unit #3 Optical Pyrometers",
      assetModel: "VP-OP-402 • High-Precision Infrared Pyrometer",
      serial: "VP-2023-4419",
      firmware: "v2.8.4 (Standard Calibration Spec)",
      placement: "Cherepovets Plant, CCU #3,<br />Strand 2 Tundish Zone",
      projectName: "PRJ-VP-8802 (Continuous Caster Modernization)",
      projectId: "PRJ-VP-8802",
      interface: "EtherNet/IP • Dual Optical Channel",
      diagramDoc: "DOC-WD-7721-04",
      messages: [
        {
          sender: "Alexey R. Danilov",
          role: "Client - Chief Instrumentation Eng.",
          avatar:
            "https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV",
          time: "08:30 AM MSK",
          text: "Ahead of the scheduled 1,400°C heat run at 14:00, pyrometer OP-402 on Strand #2 showed a slight +2.4°C baseline zero-drift during cold-reference check. Requesting remote verification of emissivity coefficient tables.",
        },
        {
          sender: "Anna Timofeeva",
          role: "VOSTOKPRIBOR - Calibration Lead",
          initials: "AT",
          time: "08:54 AM MSK",
          text: "Acknowledged, Alexey. I have pulled the thermal calibration curve from October 12. The emissivity constant is locked at ε = 0.885. Remote compensation trim will be applied over EtherNet/IP gateway within 30 minutes.",
        },
      ],
    },
    "TCK-9399": {
      id: "TCK-9399",
      title:
        "TCK-9399: Laser Profiler LP-400 Spares Shipping & Customs Declaration",
      escalation: "LOGISTICS DISPATCH TIER-2",
      sla: "COMPLETED / RESOLVED",
      isCritical: false,
      logged: "Yesterday 14:10:00 MSK",
      dispatch: "Yesterday 14:22:00 MSK",
      remaining: "RESOLVED (DELIVERED)",
      specialist: "Logistics Support (Spares Dept)",
      assetName: "Raw Material Yard Laser Profiler",
      assetModel: "LP-400 • LiDAR Stockpile Scanner",
      serial: "VP-2022-7718",
      firmware: "v3.1.0",
      placement: "Cherepovets Plant, Stockpile Bay 4",
      projectName: "PRJ-VP-7721 (BF #5 Automation)",
      projectId: "PRJ-VP-7721",
      interface: "Fiber Optic 1000Base-FX",
      diagramDoc: "DOC-WD-7721-04",
      messages: [
        {
          sender: "Alexey R. Danilov",
          role: "Client - Chief Instrumentation Eng.",
          avatar:
            "https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV",
          time: "Yesterday 14:10 MSK",
          text: "Tracking requested for replacement quartz lens kit for scanner LP-400. Delivery to Cherepovets Central Stores requested under Waybill WB-88204.",
        },
        {
          sender: "Logistics Support",
          role: "VOSTOKPRIBOR - Spares Dept",
          initials: "LS",
          time: "Yesterday 14:22 MSK",
          text: "Consignment cleared Saint Petersburg regional customs terminal under AWB: 88204-RU-SPB. Transferred to Delovie Linii express freight. Gate arrival confirmed at Cherepovets Plant Gate #3 today at 09:15 AM.",
        },
      ],
    },
    "TCK-9351": {
      id: "TCK-9351",
      title:
        "TCK-9351: Hydraulic Pressure Sensor Array Firmware Patch v3.8.1 Compatibility",
      escalation: "FIELD RESOLVED (PLC COMPLIANT)",
      sla: "COMPLETED / RESOLVED",
      isCritical: false,
      logged: "Oct 21 09:00:00 MSK",
      dispatch: "Oct 21 09:12:00 MSK",
      remaining: "RESOLVED (TEST PASSED)",
      specialist: "Denis Sokolov (Tier-3 Field Eng.)",
      assetName: "Hot Strip Mill Hydraulic Pressure Sensor Array",
      assetModel: "VP-HPA-600 • High Pressure Transducer Array",
      serial: "VP-2023-9011",
      firmware: "v3.8.1 (Siemens S7-400 Compliant)",
      placement: "Cherepovets Plant, HSM Roughing Stand #2",
      projectName: "PRJ-VP-7721 (BF #5 Automation)",
      projectId: "PRJ-VP-7721",
      interface: "PROFIBUS DP / 1.5 Mbps",
      diagramDoc: "DOC-WD-7721-04",
      messages: [
        {
          sender: "Alexey R. Danilov",
          role: "Client - Chief Instrumentation Eng.",
          avatar:
            "https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV",
          time: "Oct 21 09:00 MSK",
          text: "Patch v3.8.1 delivered for hydraulic pressure array HPA-600. Please confirm PROFIBUS GSD file revision matches Siemens STEP 7 v5.6 hardware catalog.",
        },
        {
          sender: "Denis Sokolov",
          role: "VOSTOKPRIBOR - Tier-3 Automation Engineer",
          initials: "DS",
          time: "Oct 21 09:12 MSK",
          text: "Verified Alexey. Checksum SHA-256 matches GOST standard. GSD file VOST049A.gsd successfully compiled and bench tested on rack simulator. Zero bus faults observed across 1,000 cycle pressure pulses.",
        },
      ],
    },
  };

  /**
   * Switch active ticket view and populate telemetry/chat pane
   */
  window.selectTicket = function (ticketId) {
    const data = TICKET_DATABASE[ticketId];
    if (!data) return;
    currentActiveTicket = ticketId;

    // Update table row styling
    document.querySelectorAll(".ticket-table-row").forEach((row) => {
      const indicator = row.querySelector(".row-indicator");
      const radio = row.querySelector(".row-radio");
      if (row.id === "ticket-row-" + ticketId) {
        row.classList.add("bg-surface-container-low/50");
        if (indicator)
          indicator.className =
            "row-indicator absolute left-0 top-0 bottom-0 w-1 bg-tertiary-fixed-dim";
        if (radio) {
          radio.textContent = "radio_button_checked";
          radio.className =
            "material-symbols-outlined text-xs text-on-tertiary-container row-radio";
        }
      } else {
        row.classList.remove("bg-surface-container-low/50");
        if (indicator)
          indicator.className =
            "row-indicator absolute left-0 top-0 bottom-0 w-1 bg-transparent";
        if (radio) {
          radio.textContent = "radio_button_unchecked";
          radio.className =
            "material-symbols-outlined text-xs text-secondary row-radio";
        }
      }
    });

    // Update banner card
    const titleEl = document.getElementById("bannerTicketTitle");
    if (titleEl) titleEl.textContent = data.title;
    const escEl = document.getElementById("bannerEscalationTag");
    if (escEl) escEl.textContent = data.escalation;
    const slaEl = document.getElementById("bannerSlaTag");
    if (slaEl) slaEl.textContent = data.sla;

    const bridgeBtn = document.getElementById("bannerBridgeBtn");
    if (bridgeBtn) {
      bridgeBtn.innerHTML = `<span class="material-symbols-outlined text-sm">video_camera_front</span><span class="text-xs">Join Secure Bridge #${data.id}</span>`;
    }

    // Telemetry bar
    const logEl = document.getElementById("telemetryLoggedTime");
    if (logEl) logEl.textContent = data.logged;
    const dispEl = document.getElementById("telemetryDispatchTime");
    if (dispEl) dispEl.textContent = data.dispatch;
    const winEl = document.getElementById("telemetryResolutionWindow");
    if (winEl) winEl.textContent = data.remaining;
    const specEl = document.getElementById("telemetrySpecialist");
    if (specEl) specEl.textContent = data.specialist;

    // Asset rail
    const nameEl = document.getElementById("detailAssetName");
    if (nameEl) nameEl.textContent = data.assetName;
    const modelEl = document.getElementById("detailAssetModel");
    if (modelEl) modelEl.textContent = data.assetModel;
    const serEl = document.getElementById("detailSerial");
    if (serEl) serEl.textContent = data.serial;
    const fwEl = document.getElementById("detailFirmware");
    if (fwEl) fwEl.textContent = data.firmware;
    const placeEl = document.getElementById("detailPlacement");
    if (placeEl) placeEl.innerHTML = data.placement;

    const prjLink = document.getElementById("detailProjectLink");
    if (prjLink) {
      prjLink.textContent = data.projectName;
      prjLink.onclick = () =>
        (location.href = `ProjectListAndDetail.php?project=${data.projectId}`);
    }
    const intEl = document.getElementById("detailInterface");
    if (intEl) intEl.textContent = data.interface;

    // Render messages
    window.renderMessages(data.messages);
  };

  /**
   * Render incident message stream
   */
  window.renderMessages = function (messages) {
    const stream = document.getElementById("ticketMessageStream");
    if (!stream) return;
    const countLabel = document.getElementById("messageCountLabel");
    if (countLabel) {
      countLabel.textContent = `${messages.length} MESSAGES • LIVE STREAM REFRESH: AUTO`;
    }

    stream.innerHTML = messages
      .map((msg) => {
        if (msg.isSystem) {
          return `
                    <div class="pl-12">
                        <div class="p-unit-sm rounded bg-primary-container/10 border-l-4 border-error text-body-sm text-primary flex items-start gap-2">
                            <span class="material-symbols-outlined text-base text-error mt-0.5">smart_toy</span>
                            <div class="flex-1">
                                <div class="flex items-center justify-between font-label-caps text-label-caps text-secondary mb-0.5">
                                    <span>SYSTEM AUTOMATED AUDIT</span>
                                    <span class="font-data-mono-md text-data-mono-md">${msg.time}</span>
                                </div>
                                ${msg.text}
                            </div>
                        </div>
                    </div>
                `;
        }

        const isUser = msg.sender.includes("Danilov");
        const avatarHtml = isUser
          ? `
                <img alt="${msg.sender}" class="w-10 h-10 rounded-full object-cover border border-outline-variant/50 shrink-0" src="${msg.avatar}" />
            `
          : `
                <div class="w-10 h-10 rounded-full bg-primary-container text-tertiary-fixed flex items-center justify-center font-bold shrink-0 shadow-sm border border-tertiary-fixed/30">
                    ${msg.initials || "VP"}
                </div>
            `;

        const roleBadge = isUser
          ? `
                <span class="px-1.5 py-0.2 rounded bg-surface-container text-on-surface-variant font-technical-tag text-technical-tag">${msg.role}</span>
            `
          : `
                <span class="px-1.5 py-0.2 rounded bg-tertiary-fixed text-on-tertiary-fixed font-technical-tag text-technical-tag font-semibold">${msg.role}</span>
            `;

        const borderClass = msg.isHighlight
          ? "border-l-4 border-tertiary-fixed-dim border-outline-variant/30"
          : "border border-outline-variant/30";

        let attachmentsHtml = "";
        if (msg.attachments && msg.attachments.length) {
          attachmentsHtml = `
                    <div class="flex flex-wrap items-center gap-2 mt-3 pt-2.5 border-t border-outline-variant/20 font-technical-tag text-technical-tag">
                        ${msg.attachments
                          .map(
                            (att) => `
                            <div onclick="${att.docId ? `window.previewDocument('${att.docId}')` : `window.showToast('Diagnostic capture opened', 'info')`}"
                                class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container-lowest border border-outline-variant/60 hover:border-primary cursor-pointer transition-colors">
                                <span class="material-symbols-outlined text-xs text-secondary">${att.docId ? "description" : "image"}</span>
                                <span class="font-mono text-primary font-semibold">${att.name}</span>
                                <span class="text-secondary">(${att.size})</span>
                                <span class="material-symbols-outlined text-xs text-secondary">${att.docId ? "download" : "visibility"}</span>
                            </div>
                        `,
                          )
                          .join("")}
                    </div>
                `;
        }

        return `
                <div class="flex gap-unit-base">
                    ${avatarHtml}
                    <div class="flex-1">
                        <div class="flex items-baseline justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-headline-sm text-headline-sm text-primary font-bold">${msg.sender}</span>
                                ${roleBadge}
                            </div>
                            <span class="font-data-mono-md text-data-mono-md text-secondary">${msg.time}</span>
                        </div>
                        <div class="mt-1.5 p-unit-base rounded bg-surface-container-low ${borderClass} text-body-md text-on-surface">
                            ${msg.text}
                            ${attachmentsHtml}
                        </div>
                    </div>
                </div>
            `;
      })
      .join("");
  };

  /**
   * Handle Ctrl+Enter shortcut in reply input
   */
  window.handleReplyKey = function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === "Enter") {
      e.preventDefault();
      window.postTechnicalResponse();
    }
  };

  /**
   * Post response note into active ticket stream and simulate technician answer
   */
  window.postTechnicalResponse = function () {
    const input = document.getElementById("ticketResponseInput");
    const text = input ? input.value.trim() : "";
    if (!text) {
      if (window.showToast)
        window.showToast("Please enter a technical response note", "error");
      return;
    }

    const currentTicket = TICKET_DATABASE[currentActiveTicket];
    if (!currentTicket) return;

    const now = new Date();
    const timeStr =
      now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }) +
      " MSK";

    currentTicket.messages.push({
      sender: "Alexey R. Danilov",
      role: "Client - Chief Instrumentation Eng.",
      avatar:
        "https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV",
      time: timeStr,
      text: text,
    });

    input.value = "";
    window.renderMessages(currentTicket.messages);
    if (window.showToast)
      window.showToast(
        "Note Dispatched",
        "Technical note added to live stream.",
        "success",
      );

    // Simulate auto-response from specialist Denis Sokolov
    setTimeout(() => {
      const specTime =
        new Date().toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        }) + " MSK";
      currentTicket.messages.push({
        sender: "Denis Sokolov",
        role: "VOSTOKPRIBOR - Tier-3 Automation Engineer",
        initials: "DS",
        time: specTime,
        text: "Received and confirmed, Alexey. Telemetry registers are tracking within tolerance. Boris has completed terminal torque checks and we are validating 4-20mA loop integrity now.",
        isHighlight: true,
      });
      window.renderMessages(currentTicket.messages);
      if (window.showToast)
        window.showToast(
          "New Specialist Message",
          "Update from Denis Sokolov (Diagnostics Lead)",
          "info",
        );
    }, 1200);
  };

  /**
   * Attach sample system telemetry log
   */
  window.attachSystemLog = function () {
    const input = document.getElementById("ticketResponseInput");
    if (input) {
      const sampleDump = `\n[ATTACHMENT: skid4_gateway_telemetry_dump_${Date.now().toString().slice(-4)}.log • 4-20mA Loop Trace: 12.38mA, 12.41mA, 0.00mA (0x7E DROPOUT)]\n`;
      input.value += sampleDump;
      if (window.showToast)
        window.showToast(
          "Attachment Added",
          "Telemetry capture log attached to draft",
          "info",
        );
    }
  };

  /**
   * Insert Modbus TCP register readout snapshot
   */
  window.insertRegisterReadout = function () {
    const input = document.getElementById("ticketResponseInput");
    if (input) {
      const sampleReg = `\n[REGISTER READOUT: REG_40001: 0x4E20 (Nominal: 20000), REG_40002: 0x007E (ERR_LOOP_OPEN), REG_40003: 0x03E8 (Thermocouple Temp: 1350°C)]\n`;
      input.value += sampleReg;
      if (window.showToast)
        window.showToast(
          "Registers Attached",
          "Modbus TCP register snapshot inserted",
          "info",
        );
    }
  };

  /**
   * Save draft response locally
   */
  window.saveReplyDraft = function () {
    const input = document.getElementById("ticketResponseInput");
    if (input && input.value.trim()) {
      localStorage.setItem("support_draft_" + currentActiveTicket, input.value);
      if (window.showToast)
        window.showToast(
          "Draft Saved",
          "Draft response preserved in local storage",
          "success",
        );
    } else {
      if (window.showToast) window.showToast("Draft is empty", "info");
    }
  };

  /**
   * Filter incident tickets table
   */
  window.filterTickets = function () {
    const queryInput = document.getElementById("ticketSearchInput");
    const systemSelect = document.getElementById("ticketSystemFilter");
    const prioritySelect = document.getElementById("ticketPriorityFilter");
    const statusSelect = document.getElementById("ticketStatusFilter");

    const query = (queryInput ? queryInput.value : "").toLowerCase().trim();
    const system = systemSelect ? systemSelect.value : "all";
    const priority = prioritySelect ? prioritySelect.value : "all";
    const status = statusSelect ? statusSelect.value : "all";

    let visibleCount = 0;
    const rows = document.querySelectorAll(".ticket-table-row");

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      const rowSys = row.getAttribute("data-system");
      const rowPrio = row.getAttribute("data-priority");
      const rowStat = row.getAttribute("data-status");

      let match = true;
      if (query && !text.includes(query)) match = false;
      if (system !== "all" && rowSys !== system) match = false;
      if (priority !== "all" && rowPrio !== priority) match = false;
      if (status !== "all" && rowStat !== status) match = false;

      row.style.display = match ? "" : "none";
      if (match) visibleCount++;
    });

    const countLabel = document.getElementById("ticketCountLabel");
    if (countLabel)
      countLabel.textContent = `Showing ${visibleCount} of ${rows.length} Records`;
  };

  /**
   * Reset all ticket filters
   */
  window.resetTicketFilters = function () {
    const queryInput = document.getElementById("ticketSearchInput");
    if (queryInput) queryInput.value = "";
    const systemSelect = document.getElementById("ticketSystemFilter");
    if (systemSelect) systemSelect.value = "all";
    const prioritySelect = document.getElementById("ticketPriorityFilter");
    if (prioritySelect) prioritySelect.value = "all";
    const statusSelect = document.getElementById("ticketStatusFilter");
    if (statusSelect) statusSelect.value = "all";

    window.filterTickets();
    if (window.showToast)
      window.showToast(
        "Filters Cleared",
        "Incident filters reset to default.",
        "info",
      );
  };

  /**
   * Export incident tickets audit log as CSV
   */
  window.exportTicketAudit = function () {
    let csv =
      "Ticket ID,System,Equipment,Subject,Priority,Status,Specialist,Updated\n";
    csv +=
      "TCK-9482,SCADA Telemetry,Blast Furnace #5,Telemetry dropout on Gas Chromatography Skid #4,Critical,Escalated,Denis Sokolov,12 mins ago\n";
    csv +=
      "TCK-9460,Optical Sensors,CCU #3,Scheduled zero-drift recalibration assistance,Medium,In Progress,Anna Timofeeva,2 hours ago\n";
    csv +=
      "TCK-9399,Laser Profiler,Raw Material Yard,Replacement lens assembly shipping tracking,Low,Resolved,Logistics Support,Yesterday\n";
    csv +=
      "TCK-9351,Hydraulic Pressure,Hot Strip Mill,Firmware patch v3.8.1 validation,High,Resolved,Denis Sokolov,Oct 21\n";

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `VOSTOKPRIBOR_Support_Incident_Audit_${new Date().toISOString().slice(0, 10)}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    if (window.showToast)
      window.showToast(
        "Incident Audit Exported",
        "CSV incident file downloaded.",
        "success",
      );
  };

  /**
   * Copy shareable permalink to current ticket
   */
  window.shareTicketLink = function () {
    const url = `${window.location.origin}${window.location.pathname}?ticket=${currentActiveTicket}`;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(() => {
        if (window.showToast)
          window.showToast(
            "Ticket Link Copied",
            currentActiveTicket,
            "success",
          );
      });
    } else {
      if (window.showToast) window.showToast("Ticket Link", url, "info");
    }
  };

  /**
   * Launch encrypted WebRTC audio/video bridge dialog
   */
  window.joinSecureBridge = function () {
    const ticket =
      TICKET_DATABASE[currentActiveTicket] || TICKET_DATABASE["TCK-9482"];
    const modalHtml = `
            <div class="space-y-4 text-left">
                <div class="p-3 rounded bg-primary-container text-on-primary flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-tertiary-fixed animate-pulse">lock</span>
                        <div>
                            <div class="font-headline-sm text-headline-sm font-bold">Encrypted WebRTC Audio/Video Bridge</div>
                            <div class="font-technical-tag text-technical-tag text-primary-fixed-dim">Session: #BRIDGE-VP-${ticket.id} • GOST R 34.12-2015 256-bit</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-tertiary-fixed text-primary font-technical-tag text-technical-tag font-bold">CONNECTED</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="h-44 rounded bg-primary flex flex-col items-center justify-center text-center p-3 relative border border-outline/30">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high/20 text-tertiary-fixed flex items-center justify-center font-bold text-lg mb-2">DS</div>
                        <div class="text-on-primary font-headline-sm font-semibold">Denis Sokolov</div>
                        <div class="text-on-primary-container text-xs">Field Diagnostics Lead (Online)</div>
                        <div class="absolute bottom-2 left-2 flex items-center gap-1 font-technical-tag text-xs text-tertiary-fixed">
                            <span class="w-2 h-2 rounded-full bg-tertiary-fixed animate-ping"></span> Live Video 1080p
                        </div>
                    </div>
                    <div class="h-44 rounded bg-primary flex flex-col items-center justify-center text-center p-3 relative border border-outline/30">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high/20 text-primary-fixed-dim flex items-center justify-center font-bold text-lg mb-2">BK</div>
                        <div class="text-on-primary font-headline-sm font-semibold">Boris K.</div>
                        <div class="text-on-primary-container text-xs">Mezzanine Level 2 Technician</div>
                        <div class="absolute bottom-2 left-2 flex items-center gap-1 font-technical-tag text-xs text-secondary-fixed">
                            <span class="material-symbols-outlined text-xs">mic</span> Audio Link Active
                        </div>
                    </div>
                </div>
                <div class="p-2.5 rounded bg-surface-container-low border border-outline-variant/40 font-technical-tag text-xs space-y-1">
                    <div class="flex justify-between"><span>Bridge Participants:</span><span class="font-semibold text-primary">Alexey Danilov (You), Denis Sokolov, Boris K.</span></div>
                    <div class="flex justify-between"><span>Audio Latency:</span><span class="font-mono text-tertiary-container font-semibold">18 ms (Low jitter)</span></div>
                    <div class="flex justify-between"><span>Shared Diagnostic Screen:</span><span class="text-primary font-medium">Gateway VP-GW-09 Modbus Register Stream</span></div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button onclick="window.showToast('Microphone toggled', 'info')" class="px-3 py-1.5 rounded bg-surface-container text-primary text-xs font-semibold hover:bg-surface-container-high flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">mic</span> Toggle Mic
                    </button>
                    <button onclick="window.showToast('Screen sharing activated', 'success')" class="px-3 py-1.5 rounded bg-surface-container text-primary text-xs font-semibold hover:bg-surface-container-high flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">screen_share</span> Share Telemetry
                    </button>
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove(); window.showToast('Bridge session terminated', 'info')" class="px-4 py-1.5 rounded bg-error text-on-error text-xs font-bold hover:bg-on-error-container">
                        Leave Bridge
                    </button>
                </div>
            </div>
        `;
    if (window.openModal) {
      window.openModal(`Secure Field Bridge #${ticket.id}`, modalHtml);
    }
  };

  /**
   * Display New Support Ticket form dialog
   */
  window.showNewTicketModal = function () {
    const modalHtml = `
            <form onsubmit="event.preventDefault(); window.submitNewTicketForm();" class="space-y-4 text-left">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-label-caps text-label-caps text-secondary uppercase mb-1">Industrial Subsystem</label>
                        <select id="newTicketSystem" class="w-full h-9 px-2 bg-surface-container-low border border-outline-variant/60 rounded font-body-sm text-body-sm text-primary">
                            <option value="scada">SCADA &amp; Gateway Telemetry</option>
                            <option value="gas">Gas Analysis / GC-Skid</option>
                            <option value="optical">Optical Sensors &amp; Pyrometry</option>
                            <option value="hydraulic">Hydraulic Pressure Arrays</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-caps text-label-caps text-secondary uppercase mb-1">Incident Priority</label>
                        <select id="newTicketPriority" class="w-full h-9 px-2 bg-surface-container-low border border-outline-variant/60 rounded font-body-sm text-body-sm text-primary">
                            <option value="critical">Severity 1 (Production Impact / Critical)</option>
                            <option value="medium" selected>Severity 2 / 3 (Operational Degraded)</option>
                            <option value="low">Severity 4 (Scheduled Maintenance / Inquiries)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps text-secondary uppercase mb-1">Affected Plant Asset / Serial Number</label>
                    <input id="newTicketAsset" type="text" value="Blast Furnace #5 - SCADA Gateway VP-GW-09" class="w-full h-9 px-3 bg-surface-container-low border border-outline-variant/60 rounded font-body-sm text-body-sm text-primary" placeholder="Asset name, tag or serial" required />
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps text-secondary uppercase mb-1">Incident Summary &amp; Symptoms</label>
                    <textarea id="newTicketSummary" rows="3" class="w-full p-2.5 bg-surface-container-low border border-outline-variant/60 rounded font-body-sm text-body-sm text-primary" placeholder="Describe telemetry readings, register fault codes, or physical alarms observed..." required></textarea>
                </div>
                <div class="p-2.5 rounded bg-surface-container-low border border-outline-variant/40 font-technical-tag text-xs text-secondary flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-tertiary-container">verified_user</span>
                        <span>Guaranteed P1 SLA: &lt;15 min • Assigned SLA Tier-1 Manager: Viktor Morozov</span>
                    </div>
                    <span class="text-primary font-semibold">24/7 Hot Desk</span>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button type="button" onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-3 py-1.5 rounded bg-surface-container text-secondary text-xs font-semibold hover:text-primary">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-tertiary-fixed text-primary text-xs font-bold shadow hover:bg-tertiary-fixed-dim flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">send</span> Submit Technical Incident
                    </button>
                </div>
            </form>
        `;
    if (window.openModal) {
      window.openModal("Open New Industrial Support Ticket", modalHtml);
    }
  };

  /**
   * Submit new incident ticket form
   */
  window.submitNewTicketForm = function () {
    const modal = document.getElementById("portal-dynamic-modal");
    if (modal) modal.remove();

    const newId = "TCK-" + (9500 + Math.floor(Math.random() * 100));
    if (window.showToast) {
      window.showToast(
        "Incident Registered",
        `Ticket ${newId} created and dispatched to Tier-1 Specialists`,
        "success",
      );
    }
  };

  /**
   * Parse URL query parameters on load
   */
  document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const ticketParam = params.get("ticket");
    const newParam = params.get("new");

    if (ticketParam && TICKET_DATABASE[ticketParam]) {
      window.selectTicket(ticketParam);
    }
    if (newParam === "true") {
      setTimeout(window.showNewTicketModal, 400);
    }
  });
})();
